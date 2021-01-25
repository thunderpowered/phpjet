import {fetch2} from "../tools/fetch2";
import {setAuthorizationAction, setAuthorizedStatus} from "../actions/auth";

export const checkAuthorization = () => (
    dispatch => (
        fetch2(globalSystemRootURL + '/auth/check', {}, result => (
            dispatch(setAuthorizedStatus(result.data.auth, result.data.urls))
        ))
    )
);

export const logout = () => (
    dispatch => (
        fetch2(globalSystemRootURL + '/auth/logout', {}, result => (
            dispatch(setAuthorizedStatus(result.data.auth, null))
        ))
    )
);

export const authorizationFirstFactor = () => (
    dispatch => (
        fetch2(globalSystemRootURL + '/auth', {}, result => (
            dispatch(setAuthorizationAction(result.action))
        ))
    )
);

export const authorizationSecondFactor = () => (
    dispatch => (
        fetch2(globalSystemRootURL + '/auth/verifyCode', {}, result => (
            dispatch(setAuthorizedStatus(result.data.auth, result.data.urls))
        ))
    )
);