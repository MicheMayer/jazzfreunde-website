const subscribedNotificationUrisStorageKey = 'jazzfreunde.subscribed-notification-uris'

const isNotificationUriArray = (value: unknown): value is string[] =>
    Array.isArray(value)
    && value.every((notificationUri: unknown): notificationUri is string =>
        typeof notificationUri === 'string' && notificationUri.length > 0)

const clearStoredNotificationUris = (): void => {
    try {
        window.localStorage.removeItem(subscribedNotificationUrisStorageKey)
    } catch (error) {
        console.warn('Unable to clear invalid persisted notification subscriptions.', error)
    }
}

export const loadSubscribedNotificationUris = (): Set<string> => {
    let storedNotificationUris: string | null
    try {
        storedNotificationUris = window.localStorage.getItem(subscribedNotificationUrisStorageKey)
    } catch (error) {
        console.warn('Unable to access persisted notification subscriptions; using an empty set.', error)
        return new Set()
    }

    if (storedNotificationUris === null) {
        return new Set()
    }

    let parsedNotificationUris: unknown
    try {
        parsedNotificationUris = JSON.parse(storedNotificationUris)
    } catch (error) {
        console.warn('Unable to parse persisted notification subscriptions; using an empty set.', error)
        clearStoredNotificationUris()
        return new Set()
    }

    if (!isNotificationUriArray(parsedNotificationUris)) {
        console.warn('Persisted notification subscriptions have an invalid format; using an empty set.')
        clearStoredNotificationUris()
        return new Set()
    }

    return new Set(parsedNotificationUris)
}

export const saveSubscribedNotificationUris = (notificationUris: Set<string>): void => {
    try {
        window.localStorage.setItem(
            subscribedNotificationUrisStorageKey,
            JSON.stringify(Array.from(notificationUris)),
        )
    } catch (error) {
        console.warn('Unable to persist notification subscriptions.', error)
    }
}
