<?php declare(strict_types = 1);

namespace Jazzfreunde\App\Tests\Api\Notification;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Jazzfreunde\App\Entity\Contract\ConfirmationContract;
use Jazzfreunde\App\Entity\Notification\NotificationSubscription;
use Jazzfreunde\UnitTest\Fixtures\KnownEmailsFixture;
use Jazzfreunde\App\Type\Enum\Contract\ConfirmationStateEnum;
use Jazzfreunde\App\Type\Primitive\Email;
use Jazzfreunde\App\Type\Primitive\Uri;
use Jazzfreunde\UnitTest\Fixtures\CallbackFixture;
use Jazzfreunde\UnitTest\Trait\SetupDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Tests for {@see NotificationSubscription}.
 */
final class NotificationSubscriptionTest extends WebTestCase
{
    use SetupDatabaseTrait;

    /**
     * Test creating a notification subscription via the API.
     */
    public function testCreateSubscription(): void
    {
        $client = static::createClient();
        $this->initDatabase(static::$kernel);
        $this->applyFixtures(static::$kernel, new KnownEmailsFixture());

        $container = static::getContainer();

        /** @var ManagerRegistry $registry */
        $registry = $container->get(ManagerRegistry::class);

        $content = json_encode([
            'resourceScope' => '/events/123',
            'email' => 'test@mail.com',
        ]);

        $client->request(
            Request::METHOD_POST,
            '/api/notification_subscriptions',
            server: [
                'CONTENT_TYPE' => 'application/json',
            ],
            content: $content
        );

        $this->assertResponseIsSuccessful();

        /** @var NotificationSubscription $subscription */
        $subscription = $registry->getRepository(NotificationSubscription::class)->findOneBy(['email' => 'test@mail.com']);
        $this->assertNotNull($subscription);
        $this->assertEquals('/events/123', $subscription->resourceScope->value());
        $this->assertEquals('test@mail.com', $subscription->email->value());

        $contract = $subscription->confirmation;
        $this->assertNotNull($contract);
        $this->assertEquals($contract->state, ConfirmationStateEnum::Pending);

        $email = self::getMailerMessage();
        self::assertNotNull($email);
        self::assertEmailSubjectContains($email, 'Bestätigen Sie Ihre Newsletter Anmeldung');
    }

    /**
     * Test creating a notification subscription that is already subscribed via the API.
     */
    public function testCreateSubscription_Already_Subscribed(): void
    {
        $client = static::createClient();
        $this->initDatabase(static::$kernel);
        $this->applyFixtures(
            static::$kernel,
            new KnownEmailsFixture(),
            new CallbackFixture(callback: function (ObjectManager $manager) {
                $subscription = new NotificationSubscription();
                $subscription->resourceScope = Uri::from('/events/123');
                $subscription->email = Email::from('test@mail.com');
                $subscription->confirmation = new ConfirmationContract();
                $subscription->confirmation->state = ConfirmationStateEnum::Confirmed;
                $manager->persist($subscription);
                $manager->flush();
            })
        );

        $content = json_encode([
            'resourceScope' => '/events/123',
            'email' => 'test@mail.com',
        ]);

        $client->request(
            Request::METHOD_POST,
            '/api/notification_subscriptions',
            server: [
                'CONTENT_TYPE' => 'application/json',
            ],
            content: $content
        );

        $this->assertResponseStatusCodeSame(409, 'Expected a 409 Conflict response for already subscribed email.');

        /** @var array{status:int, type:string, title:string, detail:string} $response */
        $response = json_decode((string) $client->getResponse()->getContent(), true, flags: JSON_THROW_ON_ERROR);
        $this->assertSame(409, $response['status']);
        $this->assertSame('Already subscribed', $response['title']);
        $this->assertSame('User with this email is already subscribed to this resource scope.', $response['detail']);
        $this->assertStringContainsString('/problems/already-subscribed', $response['type']);

        $email = self::getMailerMessage();
        self::assertNull($email);
    }

    public function testAlreadySubscribedErrorSampleEndpoint(): void
    {
        $client = static::createClient();

        $client->request(Request::METHOD_GET, '/api/already_subscribed_errors.json');

        $this->assertResponseStatusCodeSame(409);

        /** @var array{status:int, type:string, title:string, detail:string} $response */
        $response = json_decode((string) $client->getResponse()->getContent(), true, flags: JSON_THROW_ON_ERROR);
        $this->assertSame(409, $response['status']);
        $this->assertSame('Already subscribed', $response['title']);
        $this->assertSame('User with this email is already subscribed to this resource scope.', $response['detail']);
        $this->assertStringContainsString('/problems/already-subscribed', $response['type']);
    }

    public function testRetrySubscribingErrorSampleEndpoint(): void
    {
        $client = static::createClient();

        $client->request(Request::METHOD_GET, '/api/retry_subscribing_errors.json');

        $this->assertResponseStatusCodeSame(409);

        /** @var array{status:int, type:string, title:string, detail:string} $response */
        $response = json_decode((string) $client->getResponse()->getContent(), true, flags: JSON_THROW_ON_ERROR);
        $this->assertSame(409, $response['status']);
        $this->assertSame('Retry subscribing', $response['title']);
        $this->assertSame('A subscription with this email is already in progress for this resource scope.', $response['detail']);
        $this->assertStringContainsString('/problems/retry-subscribing', $response['type']);
    }
}