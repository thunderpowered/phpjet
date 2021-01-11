import React, {Component} from 'react';
import {fetch2} from "../../../../../../helpers/fetch2";

// MAIN PAGE BUILDER COMPONENT
// VERSION 1
export class WindowPageBuilder_v1 extends Component {
    constructor() {
        super();
        this.state = {template: {}, page: {}};
        this.urlLoadPage = globalSystemRootURL + globalSystemActions['loadPage'];
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

        // some other preparations
        // load all chunks
        // load all templates
        // select default template
        this.setState(() => (
            {
                template: [
                    // banner
                    {
                        'container': 'container-fluid',
                        'rows': [
                            {
                                'row': 'col-12',
                                'chunks': [
                                    {
                                        // banner chunk
                                    }
                                ]
                            }
                        ]
                    },
                    // header
                    {
                        'container': 'container',
                        'rows': [
                            {
                                'row': 'col-3',
                                'chunks': [
                                    {
                                        // logo
                                    }
                                ]
                            },
                            {
                                'row': 'col-6',
                                'chunks': [
                                    {
                                        // menu
                                    }
                                ]
                            },
                            {
                                'row': 'col-3',
                                'chunks': [
                                    {
                                        // search
                                    }
                                ]
                            }
                        ]
                    },
                    // main content
                    {
                        'container': 'container',
                        'rows': [
                            {
                                'row': 'col-3',
                                'chunks': [
                                    {
                                        // left sidebar
                                    }
                                ]
                            },
                            {
                                'row': 'col-6',
                                'chunks': [
                                    {
                                        // center content
                                    }
                                ]
                            },
                            {
                                'row': 'col-3',
                                'chunks': [
                                    {
                                        // right sidebar
                                    }
                                ]
                            }
                        ]
                    },
                    // footer
                    {
                        'container': 'container',
                        'rows': [
                            {
                                'row': 'col-3',
                                'chunks': [
                                    {
                                        // idk
                                    }
                                ]
                            },
                            {
                                'row': 'col-6',
                                'chunks': [
                                    {
                                        // idk
                                    }
                                ]
                            },
                            {
                                'row': 'col-3',
                                'chunks': [
                                    {
                                        // idk
                                    }
                                ]
                            }
                        ]
                    },
                ]
            }
        ), () => this.recreatePageByArray());
    }

    recreatePageByArray() {
        // load and show the template
        // we assume that everything is loaded fine and the structure is correct
        let page = {};
        page.elements = [...this.state.template];
        page.elements.forEach((container, containerIndex) => {
            // outer loop through containers
            container.rows.forEach((row, rowIndex) => {
                // middle loop through rows
                row.chunks.forEach((chunk, chunkIndex) => {
                    // inner loop through chunks
                    row.chunks[chunkIndex] = this._pb_createChunk(chunk);
                });
                container.rows[rowIndex] = this._pb_createRow(row.chunks, row.row);
            });
            page.elements[containerIndex] = this._pb_createContainer(container.rows, container.container);
        });

        this.setState(() => ({
            'page': page
        }), () => this.props.onLoaded());
    }

    _pb_createContainer(innerJSX, containerType = 'container') {
        return <div className={`PageBuilder__container theme__background-color3 mt-4 mb-4 p-3 ${containerType}`}>
            <span className="PageBuilder__label d-block p-2 pt-3 pb-4">
                Container
            </span>
            <div className="PageBuilder__row row h-100">
                {innerJSX}
            </div>
        </div>
    }

    _pb_createRow(innerJSX, rowType = 'col-3') {
        return <div className={`PageBuilder__column h-100 ${rowType}`}>
            <div title="Drag elements here"
                 className="PageBuilder__chunk-container p-2 position-relative user-select-none">
                {innerJSX}
                <span
                    className={'PageBuilder__column-label d-block position-absolute theme__fixed-absolute--center-keep-width fw-bold fs-3'}>{rowType}</span>
            </div>
        </div>
    }

    _pb_createChunk(chunk) {
        return <div className={`PageBuilder__chunk p-2 w-100 h-100`}/>
    }

    workspaceStateRollBack() {
        console.log('roll back');
    }

    workspaceStateRollForward() {
        console.log('roll forward');
    }

    workspaceSavePage() {
        console.log('save page');
    }

    workspaceSaveTemplate() {
        console.log('save template');
    }

    render() {

        return <div id={'PageBuilder'} className={'theme__background-color3'}>
            <div className="container-fluid">
                <div className="row">
                    <div className="col-12">
                        <div className="PageBuilder__controls pt-0 pb-3">
                            <div className="d-flex justify-content-between align-items-center">
                                <div className="PageBuilder__workspace-controls d-flex">
                                    {/* page controls */}
                                    {/* todo: states (active/inactive) if no such available operations */}
                                    <div onClick={this.workspaceStateRollBack}
                                         className="p-0 pt-3 pb-3 theme__cursor-pointer theme__link-color--hover"
                                         title={'Roll back to previous state'}><i className="fas fa-undo"/><span
                                        className="p-3 pb-0 pt-0">Undo</span></div>
                                    <div onClick={this.workspaceStateRollForward}
                                         className="p-3 theme__cursor-pointer theme__element-inactive"
                                         title={'Roll forward to next state'}><i className="fas fa-redo"/><span
                                        className="p-3 pb-0 pt-0">Redo</span></div>
                                </div>
                                {/* page buttons */}
                                <div
                                    className="PageBuilder__workspace-buttons d-flex justify-content-between align-items-center flex-nowrap">
                                    <div onClick={this.workspaceSaveTemplate}
                                         className="p-3 theme__cursor-pointer theme__link-color--hover"
                                         title={'//todo'}><i className="fas fa-file-export"/><span
                                        className="p-3 pb-0 pt-0">Save as Template</span></div>
                                    <div onClick={this.workspaceSavePage}
                                         className="theme__flex-basis-0 text-center p-2 theme__cursor-pointer theme__background-color--accent-soft theme__background-color--accent-soft--hover"
                                         title={'Save the page'}>
                                        <i className="fas fa-file-export"/>
                                        <span className="p-2 pb-0 pt-0">Save</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {/* workspace */}
            <div className="pageBuilder__workspace">
                <div className="container-fluid">
                    <div className="row">
                        <div className="col-10">
                            <div className="PageBuilder__body p-3 pt-1 pb-1 theme__background-color2">
                                <span className="PageBuilder__label d-block p-2 pt-3">
                                    Page Structure
                                </span>
                                {Object.keys(this.state.page).length &&
                                this.state.page.elements
                                }
                            </div>
                        </div>
                        <div className="col-2">
                            <div className="PageBuilder__sidebar theme__background-color2 p-2 pt-0 d-flex flex-column">
                                <div className="PageBuilder__sidebar-section p-2 pt-0 mb-5">
                                    <div
                                        className="PageBuilder__sidebar-section__title p-3 pt-4 pb-4 theme__border-bottom theme__border-color">
                                        <i className="fas fa-cogs"/><span className="p-3 pb-0 pt-0">Page Settings</span>
                                    </div>
                                    <div
                                        className="PageBuilder__sidebar-section__item p-2 pt-4 pb-2 d-flex justify-content-start align-items-center">
                                        <input title="This item cannot be disabled" type={'checkbox'} checked={true}
                                               disabled={true} name={'pb_page_cache'} id={'pb_page_cache'}/>
                                        <label className={"p-3 pb-0 pt-0 d-block"} htmlFor={'pb_page_cache'}>Enable
                                            cache</label>
                                    </div>
                                    <div
                                        className="PageBuilder__sidebar-section__item p-2 d-flex justify-content-start align-items-center">
                                        <input title={'Page URL'}
                                            className={'w-100 theme__border theme__border-color d-block p-2 theme__background-color3 theme__text-color'}
                                            type={'text'} minLength={8} maxLength={60} placeholder={'Page URL...'}
                                            name={'pb_page_url'} id={'pb_page_url'}/>
                                    </div>
                                    <div
                                        className="PageBuilder__sidebar-section__item p-2 d-flex justify-content-start align-items-center">
                                        <input title={'Page title'}
                                            className={'w-100 theme__border theme__border-color d-block p-2 theme__background-color3 theme__text-color'}
                                            type={'text'} minLength={8} maxLength={60} placeholder={'Page title...'}
                                            name={'pb_page_title'} id={'pb_page_title'}/>
                                    </div>

                                    {/* sidebar footer */}
                                    <div className="p-2">
                                        <a className='d-block w-100 text-left theme__link-color--hover' href={'//todo'}>Page Builder Documentation</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    }
}