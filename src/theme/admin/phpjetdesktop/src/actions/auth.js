export const CHECK_AUTHORIZATION = 'CHECK_AUTHORIZATION';

export const checkAuthorization = (authorized, urls) => ({
    // moved to Authenticator.js
    type: CHECK_AUTHORIZATION,
    authorized,
    urls
});