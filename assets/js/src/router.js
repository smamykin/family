let array = ['?myParam=1', '', '',''];
function cssFilterApplied(target)
{
    target.style["text-decoration"] = 'line-through';
    target.style['cursor'] = 'default';
    target.style['pointer-events'] = 'none';
}

function cssFilterReset(target)
{
    target.style["text-decoration"] = '';
    target.style['cursor'] = '';
    target.style['pointer-events'] = '';
}
export function router(event)
{
    if (typeof event.target.order !== 'undefined') {
        array[1] = '&order[name]=' + event.target.order;
        return '/api/products'+array[0]+array[1]+array[2]+array[3]
    } else if (event.target.id === 'filter-with-images-only') {
        cssFilterApplied(event.target);
        array[2] = '&exists[image]=true';
        return '/api/products'+array[0]+array[1]+array[2]+array[3]
    } else if (event.target.value === 'search') {
        let imagesFilter = document.getElementById('filter-with-images-only');
        cssFilterReset(imagesFilter);
        array[1] = '';
        array[2] = '';
        array[3] = '&name='+document.getElementById("searched-text").value;
        return '/api/products'+array[0]+array[1]+array[2]+array[3]
    } else if (typeof event.target.nextLink !== 'undefined') {
        return event.target.nextLink
    } else {
        array[1] = "";
        array[2] = "";
        array[3] = "";
        let imagesFilter = document.getElementById('filter-with-images-only');
        cssFilterReset(imagesFilter);
        return '/api/products?myParam=1';
    }
}
