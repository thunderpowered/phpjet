import React, {Component} from 'react';
import {fetch2} from "../../../../../../helpers/fetch2";

export class WindowPageBuilder extends Component {
    constructor() {
        super();
        this.urlLoadPage = '/admin/pages/loadPage'
    }
    componentDidMount() {
        this.loadPage(this.props.windowData.pageID);
    }

    loadPage(pageID) {
        return fetch2(this.urlLoadPage, {queryParams: {'page_id': pageID}}, {
            onSuccess: (result) => {
                if (typeof result.data !== 'undefined' && typeof result.data.page !== 'undefined' && result.data.page.id === pageID) {
                    this.preparePageBuilder(result.data.page);
                } else {
                    Msg.error('Page Builder cannot be initialized. Please, try again.')
                }
            }
        });
    }

    preparePageBuilder(page) {
        console.log(page);
        // do something
        this.props.onLoaded();
    }

    render() {
        return <div>
            Page Builder {this.props.parent}
        </div>
    }
}