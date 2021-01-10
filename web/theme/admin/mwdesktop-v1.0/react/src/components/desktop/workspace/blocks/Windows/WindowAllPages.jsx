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
    }

    loadPages() {
        fetch2(this.urlLoadPages, {}, {
            onSuccess: (result) => {
                if (typeof result.data !== 'undefined' && typeof result.data.pages !== 'undefined') {
                    this.setState(() => ({pages: result.data.pages}), () => {
                        this.props.onLoaded(() => {
                            this.dataTable = setDataTable(this.tableID, 25, [1, 2]);
                            setDataTableResponsive(this.dataTable, this.parentDivRef, this);
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
            <table id={this.tableID} className={'w-100'}>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>URL</th>
                    <th>Commentary</th>
                    <th>Created</th>
                    <th />
                </tr>
                </thead>
                <tbody>
                {this.state.pages.map(item => (
                    <tr>
                        <td>{item.id}</td>
                        <td>{item.url}</td>
                        <td>{item.comment}</td>
                        <td>{item.since}</td>
                        <td><div className={'p-2 text-center user-select-none theme__background-color--hover theme__background-color2 theme__cursor-pointer'} onClick={() => this.openPageBuilder(item.id)}>Open PageBuilder</div></td>
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