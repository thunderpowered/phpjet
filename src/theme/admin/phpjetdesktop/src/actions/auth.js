export const SET_AUTHORIZED_STATUS = 'SET_AUTHORIZED_STATUS';
export const SET_AUTHORIZATION_ACTION = 'SET_AUTHORIZATION_ACTION';

export const setAuthorizedStatus = (authorized, urls) => ({
    type: SET_AUTHORIZED_STATUS,
    authorized,
    urls
});

export const setAuthorizationAction = action => ({
    type: SET_AUTHORIZATION_ACTION,
    action
});