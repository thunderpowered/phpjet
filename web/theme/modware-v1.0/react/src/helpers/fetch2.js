// https://github.com/github/fetch/issues/256
export function fetch2(url, options = {}) {
    options = {
        // your default options
        method: 'POST',
        credentials: 'same-origin',
        redirect: 'error',
        ...options,
    };

    if (options.queryParams) {
        url += (url.indexOf('?') === -1 ? '?' : '&') + queryParams(options.queryParams);
        delete options.queryParams;
    }

    return fetch(url, options);
}

export function queryParams(params) {
    return Object.keys(params)
        .map(k => encodeURIComponent(k) + '=' + encodeURIComponent(params[k]))
        .join('&');
}