import React, {Component} from 'react';
import {fetch2} from "../../../../../helpers/fetch2";
import {setDataTable, setDataTableResponsive} from "../../../../../helpers/DataTables";

export class WindowAllPages extends Component {
    constructor() {
        super();
        this.state = {pages: []};
        this.urlLoadPages = globalSystemRootURL + globalSystemActions['loadPages'];
        this.parentDivRef = React.createRef();
        this.tableID = 'AllPagesTable';
        this.loadPages();
        this.dataTable = null;
    }

    loadDataTables() {
        if (this.dataTable) {
            // return false;
        }

        // todo just make fucking datatables work properly! it crashes every time with no visible reason
        try {
            this.dataTable = setDataTable(this.tableID, 25, [1, 2]);
            setDataTableResponsive(this.dataTable, this.parentDivRef, this);
        } catch (e) {
            this.dataTable = null;
            Msg.error('DataTables error. Please, reload the Desktop.');
            return false;
        }
    }

    loadPages() {
        fetch2(this.urlLoadPages, {}, {
            onSuccess: (result) => {
                if (typeof result.data !== 'undefined' && typeof result.data.pages !== 'undefined') {
                    this.setState(() => ({pages: result.data.pages}), () => {
                        this.props.onLoaded(() => {
                            this.loadDataTables();
                        });
                    })
                }
            }
        });
    }

    openPageBuilder(pageID) {
        this.props.openChildWindow(0, {'pageID': +pageID});
    }

    render() {
        return <div id={'WindowAllPages'} ref={this.parentDivRef}>
            {/* tools */}
            <div className="PageBuilder__tools-container p-2 pb-4 pt-3 mb-5 d-flex justify-content-start align-items-center position-relative theme__border-color theme__border-bottom">
                <div className="p-3 pt-0 pb-0">
                    <div onClick={() => this.openPageBuilder(0)}
                         className="PageBuilder__tools-item p-3 user-select-none theme__cursor-pointer theme__background-color--hover">
                        <div className="p-3 text-center mt-2">
                            <i className="fas fa-magic fs-4"/>
                            {/*<i className="fas fa-plus fs-4"/>*/}
                        </div>
                        <div className="text-center text-wrap">
                            Create Page
                        </div>
                    </div>
                </div>
            </div>

            {/* all pages */}
            <table id={this.tableID} className={'w-100'}>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>URL</th>
                    <th>Title</th>
                    <th>Created</th>
                    <th />
                </tr>
                </thead>
                <tbody>
                {this.state.pages.map(item => (
                    <tr>
                        <td>{item.id}</td>
                        <td>{item.url}</td>
                        <td>{item.title}</td>
                        <td>{item.since}</td>
                        <td><div className={'p-2 text-center user-select-none theme__background-color--hover theme__background-color2 theme__cursor-pointer'} onClick={() => this.openPageBuilder(item.id)}><i className="fas fa-magic"/><span className={'p-3 pt-0 pb-0'}>Open PageBuilder</span></div></td>
                    </tr>
                ))}
                </tbody>
                <tfoot>
                <tr>
                    <th>ID</th>
                    <th>URL</th>
                    <th>Commentary</th>
                    <th>Created</th>
                    <th />
                </tr>
                </tfoot>
            </table>
        </div>
    }
}