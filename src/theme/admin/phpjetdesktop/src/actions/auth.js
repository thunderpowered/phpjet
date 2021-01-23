export const SET_AUTHORIZED_STATUS = 'SET_AUTHORIZED_STATUS';

export const changeAuthorizedStatus = (authorized, urls) => ({
    type: SET_AUTHORIZED_STATUS,
    authorized,
    urls
});