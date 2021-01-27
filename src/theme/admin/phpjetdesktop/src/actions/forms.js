export const SET_DISABLED_STATUS = 'SET_DISABLED_STATUS';

export const setDisabledStatus = (formID, status) => ({
    type: SET_DISABLED_STATUS,
    formID,
    status
});