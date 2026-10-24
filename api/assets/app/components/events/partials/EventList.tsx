import React, { useState } from 'react'
import type { Event } from '@models/events/events.model'
import { EventListItem } from './EventListItem'
import { EventNotificationSubscription } from './EventNotificationSubscription'

export interface EventListProps {
    /**
     * The events to display
     */
    events: Event[]
    notificationPrivacyUrl: string
    subscribedNotificationUris: Set<string>
    onSubscribed: (notificationUri: string) => void
}

/**
 * Renders a list of events
 * @param events The events to display
 * @returns A list of events
 */
export default ({
    events,
    notificationPrivacyUrl,
    subscribedNotificationUris,
    onSubscribed,
}: EventListProps) => {
    const [notificationEvent, setNotificationEvent] = useState<Event | null>(null)

    return (
        <div className="flex flex-col gap-10 w-full">
            {events.map(event => (
                <EventListItem
                    key={event.id}
                    event={event}
                    isSubscribed={event.notificationSource != null && subscribedNotificationUris.has(event.notificationSource.resourceScope)}
                    onNotify={setNotificationEvent}
                />
            ))}
            <EventNotificationSubscription
                eventId={notificationEvent?.id ?? null}
                notificationSource={notificationEvent?.notificationSource ?? null}
                isOpen={notificationEvent !== null}
                onClose={() => setNotificationEvent(null)}
                onSubscribed={onSubscribed}
                notificationPrivacyUrl={notificationPrivacyUrl}
            />
        </div>
    )
}