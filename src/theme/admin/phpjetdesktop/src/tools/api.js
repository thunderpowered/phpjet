import {API_BASE_URL} from "../constants/Api";
import {TOKEN} from "../constants/Token";
import {shoutOut} from "./alert";

class Api {
    constructor(apiBaseUrl, token) {
        this.apiBaseUrl = apiBaseUrl;
        this.token = token;
    }

    get(url, options = {}, callbackOnSuccess, callbackOnError, json = true) {
        this.#__fetch('GET', url, options = {}, callbackOnSuccess, callbackOnError, json);
    }

    post(url, options = {}, callbackOnSuccess, callbackOnError, json = true) {
        this.#__fetch('POST', url, options = {}, callbackOnSuccess, callbackOnError, json);
    }

    put(url, options = {}, callbackOnSuccess, callbackOnError, json = true) {
        this.#__fetch('PUT', url, options = {}, callbackOnSuccess, callbackOnError, json);
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
        if (typeof this.apiBaseUrl === 'undefined') {
            throw new Error('API_BASE_URL is not defined');
        }
        if (typeof this.token === 'undefined') {
            throw new Error('TOKEN is not defined');
        }
        options = {
            method: method,
            credentials: 'same-origin',
            redirect: 'error',
            headers: {
                'Content-Type': 'application/json'
            },
            ...options,
        };
        if (!options.queryParams) {
            options.queryParams = {};
        }
        if (typeof options.queryParams.__csrf === 'undefined' || !options.queryParams.__csrf) {
            options.queryParams.__csrf = this.token;
        }
        options.body = method === 'GET' ? null : json ? JSON.stringify(options.queryParams) : options.queryParams;
        delete options.queryParams;
        return fetch(this.apiBaseUrl + url, options)
            .then(
                result => {
                    if (result.ok) {
                        return result.json();
                    } else {
                        throw new Error(`${result.status} ${result.statusText} ${result.url}`);
                    }
            })
            .then(
                result => {
                    if (typeof callbackOnSuccess !== 'undefined') {
                        callbackOnSuccess(result);
                    }
                    if (typeof result.messageBox !== 'undefined' && typeof result.messageBox.text !== 'undefined' && result.messageBox.text) {
                        let style = 'info';
                        if (typeof result.messageBox.style !== 'undefined') {
                            style = result.messageBox.style;
                        }
                        shoutOut(result.messageBox.text, style);
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

