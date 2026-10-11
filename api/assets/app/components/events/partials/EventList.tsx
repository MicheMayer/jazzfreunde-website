import React, { useState } from 'react'
import type { Event } from '@models/events/events.model'
import { EventListItem } from './EventListItem'
import { EventNotificationSubscription } from './EventNotificationSubscription'

export interface EventListProps {
    /**
     * The events to display
     */
    events: Event[]
}

/**
 * Renders a list of events
 * @param events The events to display
 * @returns A list of events
 */
export default ({ events }: EventListProps) => {
    const [notificationEventId, setNotificationEventId] = useState<number | null>(null)
    const [subscribedEventIds, setSubscribedEventIds] = useState<Set<number>>(() => new Set())

    return (
        <div className="flex flex-col gap-10 w-full">
            {events.map(event => (
                <EventListItem
                    key={event.id}
                    event={event}
                    isSubscribed={subscribedEventIds.has(event.id)}
                    onNotify={setNotificationEventId}
                />
            ))}
            <EventNotificationSubscription
                eventId={notificationEventId}
                isOpen={notificationEventId !== null}
                onClose={() => setNotificationEventId(null)}
                onSubscribed={eventId => setSubscribedEventIds(ids => new Set(ids).add(eventId))}
            />
        </div>
    )
}