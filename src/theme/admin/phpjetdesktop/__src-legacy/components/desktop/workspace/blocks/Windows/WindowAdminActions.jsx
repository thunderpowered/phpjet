import React, {Component} from 'react';
import {fetch2} from "../../../../../helpers/fetch2";
import {setDataTable, setDataTableResponsive} from "../../../../../helpers/DataTables";

export class WindowAdminActions extends Component {
    constructor() {
        super();
        this.url = globalSystemRootURL + globalSystemActions['getAdminActions'];
        this.state = {rows: []};
        this.tableID = 'WindowAdminActionsTable';
        this.parentDivRef = React.createRef();
    }

    componentDidMount() {
        this.loadRows();
    }

    loadRows() {
        return fetch2(this.url, {}, {
            onSuccess: (result) => {
                if (typeof result.data !== 'undefined' && typeof result.data.rows !== 'undefined') {
                    this.setState(() => ({rows: result.data.rows}), () => {
                        // notice Window that everything is fine and so it can show the content
                        this.props.onLoaded(() => {
                            this.dataTable = setDataTable(this.tableID, 50, [1, 2, 3, 4, 5, 6, 7]);
                            setDataTableResponsive(this.dataTable, this.parentDivRef, this);
                        });
                    });
                }
            }
        });
    }

    render() {
        if (!this.state.rows.length) {
            return <div>Loading...</div>
        } else {
            return <div id={'WindowAdminActions'} ref={this.parentDivRef}>
                <table id={this.tableID} className={'w-100'}>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Admin ID</th>
                        <th>Action</th>
                        <th>Status</th>
                        <th>Commentary</th>
                        <th>URL</th>
                        <th>IP</th>
                        <th>User Agent</th>
                        <th>Date and Time</th>
                        <th>POST Data</th>
                    </tr>
                    </thead>
                    <tbody>
                    {this.state.rows.map((item) => (
                        <tr className={`theme__background-color3-i ${item.status === 'Fail' ? 'theme__link-color--accent2' : ''}`}>
                            {Object.keys(item).map((_index) => (
                                <td>{item[_index]}</td>
                            ))}
                        </tr>
                    ))}
                    </tbody>
                    <tfoot>
                    <tr>
                        {Object.keys(this.state.rows[0]).map((item) => (
                            <th>
                                {item}
                            </th>
                        ))}
                    </tr>
                    </tfoot>
                </table>
            </div>
        }
    }
}