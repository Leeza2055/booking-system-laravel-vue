export function useTimeFormat() {
    const formatTime = (time: string): string => {
        const [hours, minutes] = time.split(':')
        const h = parseInt(hours)
        const ampm = h >= 12 ? 'PM' : 'AM'
        const hour12 = h % 12 || 12

        return `${hour12}:${minutes} ${ampm}`
    }

    return { formatTime }
}
