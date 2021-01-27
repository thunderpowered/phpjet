import {fetch2} from "../tools/fetch2";
import {setAuthorizationAction, setAuthorizedStatus} from "../actions/auth";

export const checkAuthorization = callback => (
    dispatch => (
        fetch2(globalSystemRootURL + '/auth/check', {}, result => (
            dispatch(setAuthorizedStatus(result.data.auth, result.data.urls))
        ))
    )
);

export const logout = callback => (
    dispatch => (
        fetch2(globalSystemRootURL + '/auth/logout', {}, result => (
            dispatch(setAuthorizedStatus(result.data.auth, null))
        ))
    )
);

export const authorizationFirstFactor = (values, callback) => (
    dispatch => (
        fetch2(globalSystemRootURL + '/auth', {queryParams: values}, result => {
            callback(result);
            return dispatch(setAuthorizationAction(result.action))
        })
    )
);

export const authorizationSecondFactor = (values, callback) => (
    dispatch => (
        fetch2(globalSystemRootURL + '/auth/verifyCode', {queryParams: values}, result => {
            callback(result);
            return dispatch(setAuthorizedStatus(result.data.auth, result.data.urls))
        })
    )
);