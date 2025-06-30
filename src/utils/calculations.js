export function calculateGroups(totalCompletions) {
    const g1 = Math.min(Math.ceil((totalCompletions - 10) * 0.02 + 10), 30);
    const g2 = Math.min(Math.ceil((totalCompletions - 10) * 0.05 + g1), 80);
    const g3 = Math.min(Math.ceil((totalCompletions - 10) * 0.125 + g2), 205);
    const g4 = Math.min(Math.ceil((totalCompletions - 10) * 0.333 + g3), 539);
    const g5 = Math.min(Math.ceil((totalCompletions - 10) * 0.472 + g4), totalCompletions);
    return [g1, g2, g3, g4, g5];
}

export function formatDuration(recordDuration) {
    const totalSeconds = recordDuration;
    const hours = Math.floor(totalSeconds / 3600);
    const minutes = Math.floor((totalSeconds % 3600) / 60);
    const seconds = Math.floor(totalSeconds % 60);
    const milliseconds = Math.floor((totalSeconds % 1) * 1000);

    if (hours > 0) {
        return `${hours}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}.${milliseconds.toString().padStart(3, '0')}`;
    } else {
        return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}.${milliseconds.toString().padStart(3, '0')}`;
    }
}

export function formatDate(unixTimestamp) {
    const now = Date.now() / 1000;
    const secondsDifference = Math.floor(now - unixTimestamp);

    const secondsInMinute = 60;
    const secondsInHour = 3600;
    const secondsInDay = 86400;
    const secondsInWeek = 604800;
    const secondsInMonth = 2592000;
    const secondsInYear = 31536000;

    const pluralize = (value, unit) => {
        return `${value} ${unit}${value === 1 ? '' : 's'} ago`;
    };

    if (secondsDifference < secondsInMinute) {
        return pluralize(secondsDifference, 'second');
    } else if (secondsDifference < secondsInHour) {
        const minutes = Math.floor(secondsDifference / secondsInMinute);
        return pluralize(minutes, 'minute');
    } else if (secondsDifference < secondsInDay) {
        const hours = Math.floor(secondsDifference / secondsInHour);
        return pluralize(hours, 'hour');
    } else if (secondsDifference < secondsInWeek) {
        const days = Math.floor(secondsDifference / secondsInDay);
        return pluralize(days, 'day');
    } else if (secondsDifference < secondsInMonth) {
        const weeks = Math.floor(secondsDifference / secondsInWeek);
        return pluralize(weeks, 'week');
    } else if (secondsDifference < secondsInYear) {
        const months = Math.floor(secondsDifference / secondsInMonth);
        return pluralize(months, 'month');
    } else {
        const years = Math.floor(secondsDifference / secondsInYear);
        return pluralize(years, 'year');
    }
}