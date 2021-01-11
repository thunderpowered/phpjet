import {fetch2} from "../helpers/fetch2";

export class PageBuilder {
    constructor() {
        this.urlLoadPageBuilder = globalSystemRootURL + globalSystemActions['loadPageBuilder'];
        this.urlLoadPage = globalSystemRootURL + globalSystemActions['loadPage'];
    }

    loadPageBuilderData(callback) {
        return fetch2(this.urlLoadPageBuilder, {}, {
            onSuccess: (result) => {
                if (typeof result.data !== 'undefined' && typeof result.data.pageBuilder !== 'undefined') {
                    callback(result.data.pageBuilder);
                } else {
                    Msg.error('Unable to load Page Builder data. Please, reload the page and try again.')
                }
            }
        });
    }

    loadPage(pageID, callback) {
        return fetch2(this.urlLoadPage, {queryParams: {'page_id': pageID}}, {
            onSuccess: (result) => {
                if (typeof result.data !== 'undefined' && typeof result.data.page !== 'undefined' && result.data.page.id === pageID) {
                    callback(result.data.page);
                } else {
                    Msg.error('Page Builder cannot be initialized. Please, try again.')
                }
            }
        });
    }

}