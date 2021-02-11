export const SET_DISABLED_STATUS = 'SET_DISABLED_STATUS';
export const SET_INPUT_VALUE = 'SET_INPUT_VALUE';
export const CREATE_FORM = 'CREATE_FORM';

export const createForm = formID => ({
    type: CREATE_FORM,
    formID
});

export const setDisabledStatus = (formID, status) => ({
    type: SET_DISABLED_STATUS,
    formID,
    status
});

export const setInputValue = (formID, inputID, value) => ({
    type: SET_INPUT_VALUE,
    formID,
    inputID,
    value
});