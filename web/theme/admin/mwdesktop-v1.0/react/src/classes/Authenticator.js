import {fetch2} from "../helpers/fetch2";

export class Authenticator {

    constructor() {
        this.urlCheck = globalSystemHost + '/admin/auth/check';
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
                if (typeof result.data !== 'undefined' && typeof result.data.auth !== 'undefined') {
                    proceedAuthorization(result.data.auth);
                } else {
                    console.log('Not authorized');
                }
            }
        });
    }
}