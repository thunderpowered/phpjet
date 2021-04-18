import {API_BASE_URL} from "../constants/Api";
import {TOKEN} from "../constants/Token";
import {shoutOut} from "./alert";

class Api {
    constructor(apiBaseUrl, token) {
        this.apiBaseUrl = apiBaseUrl;
        this.token = token;
    }

    get(url, options = {}, callbackOnSuccess, callbackOnError, json = true) {
        this.#__fetch('GET', url, options, callbackOnSuccess, callbackOnError, json);
    }

    post(url, options = {}, callbackOnSuccess, callbackOnError, json = true) {
        this.#__fetch('POST', url, options, callbackOnSuccess, callbackOnError, json);
    }

    put(url, options = {}, callbackOnSuccess, callbackOnError, json = true) {
        this.#__fetch('PUT', url, options, callbackOnSuccess, callbackOnError, json);
    }

    file(url, options = {}, callbackOnSuccess, callbackOnError) {
        options = {
            ...options,
            headers: {},
        };
        const formData = new FormData();
        for (let key in options.queryParams) {
            if (options.queryParams.hasOwnProperty(key)) {
                formData.append(key, options.queryParams[key]);
            }
        }
        formData.append('__csrf', this.token);
        options.queryParams = formData;
        return this.#__fetch('POST', url, options, callbackOnSuccess, callbackOnError, false);
    }

    #__fetch(method, url, options = {}, callbackOnSuccess, callbackOnError, json = true) {
        if (typeof this.apiBaseUrl === 'undefined' || !this.apiBaseUrl) {
            throw new Error('API_BASE_URL is not defined or empty');
        }
        if (typeof this.token === 'undefined' || !this.token) {
            throw new Error('TOKEN is not defined or empty');
        }
        options = {
            method: method,
            credentials: 'same-origin',
            redirect: 'error',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.token // should be everywhere
            },
            ...options,
        };
        if (!options.queryParams) {
            options.queryParams = {};
        }
        url = new URL(this.apiBaseUrl + url);
        if (method === 'GET') {
            url.search = new URLSearchParams(options.queryParams).toString();
            options.body = null;
        } else {
            options.body = json ? JSON.stringify(options.queryParams) : options.queryParams;
        }
        return fetch(url, options)
            .then(
                result => {
                    return result.json();
            })
            .then(
                result => {
                    if (typeof callbackOnSuccess !== 'undefined') {
                        callbackOnSuccess(result);
                    }
                    if (typeof result.message !== 'undefined' && typeof result.message.text !== 'undefined' && result.message.text) {
                        let style = 'info';
                        if (typeof result.message.style !== 'undefined') {
                            style = result.message.style;
                        }
                        shoutOut(result.message.text, style);
                    }
                },
                error => {
                    // data errors caught here
                    shoutOut(error, 'danger');
                    console.error(error);
                })
            .catch(error => {
                // other errors caught here
                shoutOut(error, 'danger');
                console.error(error);
            });
    }
}

const api = new Api(API_BASE_URL, TOKEN);
export default api

