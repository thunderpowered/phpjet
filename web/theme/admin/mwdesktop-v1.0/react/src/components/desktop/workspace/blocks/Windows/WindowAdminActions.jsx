import React, {Component} from 'react';
import {fetch2} from "../../../../../helpers/fetch2";
import {SimpleLoader} from "../../../elements/loaders/SimpleLoader";

export class WindowAdminActions extends Component {
    constructor() {
        super();
        this.url = '/admin/statistics/getAdminActions';
        this.state = {rows: []};
        this.tableID = 'WindowAdminActionsTable';
        this.dataTable;
        this.parentDivRef = React.createRef();
        this.parentDivDimensions = {width: 0, height: 0};
        this.loadRows();
    }

    loadRows() {
        return fetch2(this.url, {}, {
            onSuccess: (result) => {
                if (typeof result.data !== 'undefined' && typeof result.data.rows !== 'undefined') {
                    this.setState(() => ({rows: result.data.rows}), () => {
                        // notice Window that everything is fine and so it can show the content
                        this.props.onLoaded(() => {

                            this.dataTable = $(`#${this.tableID}`).DataTable({
                                'responsive': true,
                                "pageLength": 25,
                                "lengthMenu": [10, 20, 25, 50, 75, 100],
                                "order": [[0, 'desc']],
                                "dom": 'Bfrtip',
                                "buttons": [
                                    {
                                        extend: 'csv',
                                        text: 'Export CSV',
                                        exportOptions: {
                                            columns: [$(":not(.donotexport)")]
                                        },
                                    },
                                    {
                                        extend: 'excel',
                                        text: 'Export Excel',
                                        exportOptions: {
                                            columns: [$(":not(.donotexport)")]
                                        },
                                    }
                                ],
                                initComplete: function () {
                                    this.api().columns([1, 2, 3, 4, 5, 6, 7]).every(function () {
                                        var column = this;
                                        var select = $('<select><option value="">_All</option><option value="NULL">_Empty</option></select>')
                                            .appendTo($(column.footer()).empty())
                                            .on('change', function () {
                                                var val = $.fn.dataTable.util.escapeRegex(
                                                    $(this).val()
                                                );
                                                if (val === 'NULL') {
                                                    column
                                                        .search('^$', true, false)
                                                        .draw();
                                                } else {
                                                    column
                                                        .search(val ? '^' + val + '$' : '', true, false)
                                                        .draw();
                                                }
                                            });

                                        column.data().unique().sort().each(function (d, j) {
                                            if (!d) {
                                                return;
                                            }
                                            select.append('<option value="' + d + '">' + d + '</option>')
                                        });
                                    });
                                }
                            });

                            this.parentDivDimensions = {
                                width: this.parentDivRef.current.offsetWidth,
                                height: this.parentDivRef.current.offsetHeight
                            };

                            // ok, i can't handle it
                            // dataTables does not react on resizing of parent div, it only reacts when window (i mean browser window) changes
                            // so i have to force redrawing somehow
                            // probably this is the ugliest way to do it, but damn it works
                            document.addEventListener('mousemove', () => {
                                if (this.parentDivDimensions.width !== this.parentDivRef.current.offsetWidth || this.parentDivDimensions.height !== this.parentDivRef.current.offsetHeight) {
                                    this.dataTable.responsive.recalc();

                                    this.parentDivDimensions = {
                                        width: this.parentDivRef.current.offsetWidth,
                                        height: this.parentDivRef.current.offsetHeight
                                    };
                                }
                            });
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