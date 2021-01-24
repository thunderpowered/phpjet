export const token = document.querySelector("meta[name=csrf_token]").getAttribute("content");

export const fetch2 = (url, options = {}, callbackOnSuccess, callbackOnError, json = true) => {
    options = {
        method: 'POST',
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
        options.queryParams.__csrf = token;
    }

    options.body = json ? JSON.stringify(options.queryParams) : options.queryParams;
    delete options.queryParams;

    return fetch(url, options)
        .then(result => result.json())
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

                    Msg[style](result.messageBox.text, 5000);
                }
            }, error => {
                if (typeof callbackOnSuccess !== 'undefined') {
                    callbackOnSuccess(error);
                }
                Msg.danger(error, 5000);
            });
};

export const queryParams = (params) => {
    return Object.keys(params)
        .map(k => encodeURIComponent(k) + '=' + encodeURIComponent(params[k]))
        .join('&');
};