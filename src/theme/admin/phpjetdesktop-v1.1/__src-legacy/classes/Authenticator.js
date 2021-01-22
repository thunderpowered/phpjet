import {fetch2} from "../helpers/fetch2";

export class Authenticator {

    constructor() {
        this.urlCheck = globalSystemRootURL + '/auth/check';
        this.urlLogout = globalSystemRootURL + '/auth/logout';
        this.recheckInterval = 60000;
    }

    isAdminAuthorized(proceedAuthorization, recheck = false) {
        if (recheck) {
            setInterval(() => {
                return this.checkAuthentication(proceedAuthorization);
            }, this.recheckInterval);
        }

        return this.checkAuthentication(proceedAuthorization);
    }

    checkAuthentication(proceedAuthorization) {
        return fetch2(this.urlCheck, {}, {
            onSuccess: (result) => {
                if (typeof result.data !== 'undefined') {
                    proceedAuthorization(result.data.auth, result.data.urls);
                }
            }
        });
    }

    logout(proceedAuthorization) {
        return fetch2(this.urlLogout, {}, {
            onSuccess: (result) => {
                if (typeof result.status !== 'undefined' && result.status) {
                    // return proceedAuthorization(false);
                    // Or we can just call check again to be sure everything is fine
                    return this.checkAuthentication(proceedAuthorization);
                }
            }
        });
    }
}