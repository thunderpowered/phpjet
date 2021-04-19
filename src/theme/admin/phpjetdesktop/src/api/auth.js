import api from "../tools/api";
import {setAuthorizationAction, setAuthorizedStatus} from "../actions/auth";

export const checkAuthorization = () => (
    dispatch => (
        api.get('auth', {}, result => (
            dispatch(setAuthorizedStatus(result.data.auth, result.data.admin_id))
        ))
    )
);

export const logout = () => (
    dispatch => (
        api.post('auth/logout', {}, result => (
            dispatch(setAuthorizedStatus(result.data.auth, null))
        ))
    )
);

export const authorizationFirstFactor = (values, callback) => (
    dispatch => (
        api.post('auth/login', {queryParams: values}, result => {
            callback(result);
            return dispatch(setAuthorizationAction(result.action))
        })
    )
);

export const authorizationSecondFactor = (values, callback) => (
    dispatch => (
        api.post('auth/verify', {queryParams: values}, result => {
            callback(result);
            return dispatch(setAuthorizedStatus(result.data.auth, result.data.admin_id))
        })
    )
);