import React, {Component} from 'react';
import {PageBuilder} from "../../../../../../classes/PageBuilder";
import Tab from 'react-bootstrap/Tab'
import Tabs from 'react-bootstrap/Tabs';
import {Draggable} from "../../../../../../helpers/Draggable";

// MAIN PAGE BUILDER COMPONENT
// VERSION 1
export class WindowPageBuilder_v1 extends Component {
    constructor() {
        super();
        this.pageBuilder = new PageBuilder();
        this.state = {
            // edit|create
            mode: 'edit',
            // all available chunks
            chunks: [],
            // all available templates
            templates: [],
            // loaded template
            template: {},
            // rendered page (contains jsx array)
            page: {},
            // history of workspace changes (for undo/redo)
            history: {}
        };
        this.draggable = new Draggable();
        this.mouseMoveCallback = () => {
        };
    }

    componentDidMount() {
        this.pageBuilder.loadPage(this.props.windowData.pageID, this.renderPage.bind(this));
        this.pageBuilder.loadPageBuilderData(this.savePageBuilderData.bind(this));
    }

    savePageBuilderData(data) {
        this.setState(() => ({
            chunks: data.chunks.map(chunk => ({...chunk, coordinates: {top: 0, left: 0}})),
            templates: data.templates
        }));
    }

    renderPage(page) {
        this.setState(() => ({'page': page}), () => this.recreatePageByArray());
    }

    // load and show the template
    // we assume that everything is loaded fine and the structure is correct
    recreatePageByArray() {
        let page = {...this.state.page};
        // outer loop through containers
        page.content.forEach((container, containerIndex) => {
            // middle loop through rows
            container.rows.forEach((row, rowIndex) => {
                // inner loop through chunks
                row.chunks.forEach((chunk, chunkIndex) => {
                    row.chunks[chunkIndex] = this._pb_createChunk(chunk);
                });
                container.rows[rowIndex] = this._pb_createRow(row.chunks, row.row);
            });
            page.content[containerIndex] = this._pb_createContainer(container.rows, container.container);
        });

        this.setState(() => ({
            'page': {...this.state.page, rendered: page.content}
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
        // drag target
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

    workspaceSaveDraft() {
        console.log('save draft');
    }

    changeSelectedTemplate() {
        console.log('set new template');
    }

    dragChunk(event, chunkIndex) {
        this.mouseMoveCallback = (mouseMoveEvent) => {
            this.draggable.dragElement(mouseMoveEvent, (newCoordinates) => {
                this.setState(() => (
                    {
                        chunks: this.state.chunks.map((chunk, index) => (index === chunkIndex ? {
                            ...chunk,
                            coordinates: newCoordinates
                        } : chunk))
                    }
                ));
            });
        };
        document.addEventListener('mousemove', this.mouseMoveCallback);

        // detect if the element above place where it should be placed


        // and don't forget to unset it
        document.addEventListener('mouseup', () => {
            document.removeEventListener('mousemove', this.mouseMoveCallback);
        })
    }

    render() {
        return <div id={'PageBuilder'} className={'theme__background-color3'}>
            <div className="container-fluid">
                <div className="row">
                    <div className="col-12 p-0">
                        <div id="PageBuilderElements"
                             className="PageBuilder__elements p-0 pt-3 pb-3 theme__border-color theme__border-bottom">
                            {/* Using react-bootstrap */}
                            <Tabs defaultActiveKey={'templates'} id={'PageBuilderElements__Navbar'}>
                                <Tab title={'Templates'} eventKey={'templates'}>
                                    <div
                                        className="PageBuilder__elements-container p-2 pb-0 pt-3 d-flex justify-content-start align-items-center">
                                        {this.state.templates &&
                                        this.state.templates.map(template => (
                                            <div className="p-3 pt-0 pb-0">
                                                <div onClick={this.changeSelectedTemplate}
                                                     className="PageBuilder__elements-item p-3 user-select-none theme__cursor-pointer theme__background-color--hover-soft">
                                                    <div className="p-3 text-center mt-2">
                                                        <i className="fas fa-columns fs-4"/>
                                                    </div>
                                                    <div className="text-center text-wrap">
                                                        {template.title}
                                                    </div>
                                                </div>
                                            </div>
                                        ))
                                        }
                                    </div>
                                </Tab>
                                <Tab title={'Chunks'} eventKey={'chunks'}>
                                    <div
                                        className="PageBuilder__elements-container p-2 pb-0 pt-3 d-flex justify-content-start align-items-center">
                                        {this.state.chunks &&
                                        this.state.chunks.map((chunk, index) => (
                                            <div
                                                className="PageBuilder__elements-item-container p-3 pt-0 pb-0 position-relative">
                                                <div onMouseDown={event => this.dragChunk(event, index)}
                                                     style={{top: chunk.coordinates.top, left: chunk.coordinates.left}}
                                                     className={"PageBuilder__elements-item p-3 user-select-none theme__cursor-pointer theme__background-color--hover-soft position-absolute"}>
                                                    <div className="p-3 text-center mt-2">
                                                        <i className="fas fa-puzzle-piece fs-4"/>
                                                    </div>
                                                    <div className="text-center text-wrap">
                                                        {chunk.name}
                                                    </div>
                                                </div>
                                            </div>
                                        ))
                                        }
                                    </div>
                                </Tab>
                            </Tabs>
                        </div>
                    </div>
                </div>
            </div>
            <div className="container-fluid">
                <div className="row">
                    <div className="col-12">
                        <div className="PageBuilder__controls pt-2 pb-2">
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
                                    <div onClick={this.workspaceSaveDraft}
                                         className="p-3 theme__cursor-pointer theme__link-color--hover"
                                         title={'//todo'}><i className="fas fa-file-signature"/><span
                                        className="p-3 pb-0 pt-0">Save as draft</span></div>
                                    {/*<div onClick={this.workspaceSaveTemplate}*/}
                                    {/*     className="p-3 theme__cursor-pointer theme__link-color--hover"*/}
                                    {/*     title={'//todo'}><i className="fas fa-paste"/><span*/}
                                    {/*    className="p-3 pb-0 pt-0">Save as template</span></div>*/}
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
                        <div className="col-xxl-9 col-xl-12 col-md-12 col-sm-12">
                            <div className="PageBuilder__body p-3 pt-1 pb-1 theme__background-color2">
                                <span className="PageBuilder__label d-block p-2 pt-3">
                                    Page Structure
                                </span>
                                {Object.keys(this.state.page).length &&
                                this.state.page.rendered
                                }
                            </div>
                        </div>
                        <div className="col-xxl-3 col-xl-12 col-md-12 col-sm-12">
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
                                        <a className='d-block w-100 text-left theme__link-color--hover' href={'//todo'}>Page
                                            Builder Documentation</a>
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