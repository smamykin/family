export function router(event)
{
    if (typeof event.target.nextLink !== 'undefined') {
        return event.target.nextLink
    } else {
        return '/api/products';
    }
}
