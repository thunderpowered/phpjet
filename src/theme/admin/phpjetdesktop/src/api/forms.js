import {setDisabledStatus} from "../actions/forms";

export const sendForm = (action, values, formID, onSubmit) => (
    dispatch => {
        dispatch(setDisabledStatus(formID, true));
        return dispatch(onSubmit(values, () => dispatch(setDisabledStatus(formID, false))));
    }
);