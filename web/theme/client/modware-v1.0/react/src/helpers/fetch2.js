// CloudStore Engine requires every POST-query to have csrf-token
import {Token} from "./token";

export function fetch2(url, options = {}, callbacks) {
    options = {
        method: 'POST',
        credentials: 'same-origin',
        redirect: 'error',
        headers: {
            'Content-Type': 'application/json'
        },
        ...options,
    };

    if (options.queryParams) {
        // check CSRF Token
        if (typeof options.queryParams.__csrf === 'undefined' || !options.queryParams.__csrf) {
            options.queryParams.__csrf = Token;
        }

        options.body = JSON.stringify(options.queryParams);
        // url += (url.indexOf('?') === -1 ? '?' : '&') + queryParams(options.queryParams);
        delete options.queryParams;
    }

    return fetch(url, options)
        .then(result => result.json())
        .then(
            result => {
                if (typeof callbacks.onSuccess !== 'undefined') {
                    callbacks.onSuccess(result);
                }
            }, error => {
                if (typeof callbacks.onError !== 'undefined') {
                    callbacks.onError(error);
                }
            });
}

export function queryParams(params) {
    return Object.keys(params)
        .map(k => encodeURIComponent(k) + '=' + encodeURIComponent(params[k]))
        .join('&');
}