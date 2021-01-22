import {Token} from "./token";
import {fetch2} from "./fetch2";

export function sendFile(url, options = {}, callbacks) {
    options = {
        method: 'POST',
        credentials: 'same-origin',
        redirect: 'error',
        headers: {},
        ...options,
    };

    let formData = new FormData();
    for (let key in options.queryParams) {
        if (options.queryParams.hasOwnProperty(key)) {
            formData.append(key, options.queryParams[key]);
        }
    }

    // don't forget to append the token
    formData.append('__csrf', Token);
    options.queryParams = formData;

    // yep, just use fetch2
    return fetch2(url, options, callbacks, false);
}