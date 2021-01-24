import {fetch2} from "../tools/fetch2";
import {changeAuthorizedStatus} from "../actions/auth";

export const checkAuthorization = () => (
    dispatch => (
        fetch2(globalSystemRootURL + '/auth/check', {}, result => (
            dispatch(changeAuthorizedStatus(result.data.auth, result.data.urls))
        ))
    )
);

export const logout = () => (
    dispatch => (
        fetch2(globalSystemRootURL + '/auth/logout', {}, result => (
            dispatch(changeAuthorizedStatus(result.data.auth, null))
        ))
    )
);