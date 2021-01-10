import React, {Component} from 'react';
import {fetch2} from "../../../../../../helpers/fetch2";

// MAIN PAGE BUILDER COMPONENT
// VERSION 1
export class WindowPageBuilder_v1 extends Component {
    constructor() {
        super();
        this.urlLoadPage =  globalSystemRootURL + globalSystemActions['loadPage'];
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

        return <div id={'PageBuilder'}>
            <div className="PageBuilder__body">

            </div>
        </div>
    }
}