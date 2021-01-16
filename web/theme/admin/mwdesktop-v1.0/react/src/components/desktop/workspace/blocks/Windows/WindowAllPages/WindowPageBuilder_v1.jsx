import React, {Component} from 'react';
import {PageBuilder} from "../../../../../../classes/PageBuilder";
import {Draggable} from "../../../../../../helpers/Draggable";
import Tab from 'react-bootstrap/Tab'
import Tabs from 'react-bootstrap/Tabs';
import {Chunk} from "./PageBuilderElements/Chunk";
import {DefinitelyNotATree} from "../../../../../../structures/DefinitelyNotATree";

// MAIN PAGE BUILDER COMPONENT
// VERSION 1
export class WindowPageBuilder_v1 extends Component {
    constructor() {
        super();
        this.pageBuilder = new PageBuilder();
        this.draggable = new Draggable();
        this.state = {
            // edit|create
            mode: 'edit',
            // all available chunks
            chunks: {
                // js-objects representing chunk structure (in terms of PageBuilder)
                structure: [],
                // react-objects needed for display info and generate events
                rendered: []
            },
            // rendered page (contains jsx array)
            page: {
                // js-object represents the page structure (including all content-json)
                structure: {},
                // array of processed React components (see recreatePageByArray function)
                rendered: {}
            },
            // all available templates
            templates: [],
            // loaded template
            template: {},
            // history of workspace changes (for undo/redo)
            history: [],
            historyCursor: -1
        };

        this.dictionary = {rows: {}};
        this.columnRefs = [];
        this.input = {};
    }

    componentDidMount() {
        this.pageBuilder.loadPage(this.props.windowData.pageID, this.renderPage.bind(this));
        this.pageBuilder.loadPageBuilderData(this.savePageBuilderData.bind(this));
    }

    savePageBuilderData(pageBuilderData) {
        this.setState(() => ({
            chunks: {
                structure: pageBuilderData.chunks,
                rendered: pageBuilderData.chunks.map((chunk, index) => (
                    <Chunk index={index} name={chunk.props.name} coordinates={{top: 0, left: 0}}
                           onDrugChunk={this.dragChunk.bind(this)}/>))
            },
            templates: pageBuilderData.templates
        }));
    }

    renderPage(currentPage) {
        let pageContent = new DefinitelyNotATree(currentPage.content);
        let pageRendered = this.recreatePageByArray2(pageContent.root);

        this.setState(() => ({
            page: {
                structure: {...currentPage, content: pageContent},
                rendered: pageRendered
            },
            history: [...this.state.history.splice(0, this.state.historyCursor + 1), {
                structure: {...currentPage, content: pageContent.returnSelf()},
                rendered: pageRendered
            }],
            historyCursor: this.state.historyCursor + 1
        }), () => {
            this.props.onLoaded();
        });
    }

    recreatePageByArray2(root = this.state.page.structure.content.root, parentNodeIdentifier = []) {
        let jsx = [];
        for (let i = 0; i < root.children.length; i++) {
            // that doesn't look right but at the moment i can't find other way
            // (except, of course, using links to items, but i tried it and it complicates working with history way too much)
            let nodeIdentifier = [...parentNodeIdentifier, i];

            let childrenJsx = [];
            if (typeof root.children[i].children !== 'undefined') {
                childrenJsx = this.recreatePageByArray2(root.children[i], nodeIdentifier);
            }

            if (typeof root.children[i].type !== 'undefined') {
                let type = root.children[i].type[0].toUpperCase() + root.children[i].type.slice(1);
                jsx.push(this[`_pb_create${type}`](childrenJsx, root.children[i], nodeIdentifier)); // returns jsx
            }
        }

        return jsx;
    }

    /**
     * @deprecated
     */
    recreatePageByArray() {
        let renderedPage = [];
        // outer loop through containers
        this.state.page.structure.content.forEach((container, containerIndex) => {
            if (typeof renderedPage[containerIndex] === 'undefined') {
                renderedPage[containerIndex] = {rows: []};
            }
            // middle loop through rows
            container.rows.forEach((row, rowIndex) => {
                if (typeof renderedPage[containerIndex].rows[rowIndex] === 'undefined') {
                    renderedPage[containerIndex].rows[rowIndex] = {chunks: []};
                }
                // inner loop through chunks
                row.chunks.forEach((chunk, chunkIndex) => {
                    renderedPage[containerIndex].rows[rowIndex].chunks[chunkIndex] = this._pb_createChunk(chunk);
                });
                renderedPage[containerIndex].rows[rowIndex] = this._pb_createRow(renderedPage[containerIndex].rows[rowIndex].chunks, row, containerIndex, rowIndex);
            });
            renderedPage[containerIndex] = this._pb_createContainer(renderedPage[containerIndex].rows, container);
        });

        this.setState(() => ({
            page: {...this.state.page, rendered: {content: renderedPage}},
            history: [...this.state.history.splice(0, this.state.historyCursor + 1), {
                ...this.state.page,
                rendered: {content: renderedPage}
            }]
        }), () => {
            this.setState(() => ({
                historyCursor: this.state.history.length - 1
            }));
            this.props.onLoaded();
        });
    }

    _pb_createContainer(innerJSX, container, containerIdentifier) {
        return <div className={`PageBuilder__container theme__background-color3 mt-4 mb-4 p-3 ${container.class}`}>
            <span className="PageBuilder__label d-block p-2 pt-3 pb-4">
                Container
            </span>
            <div className="PageBuilder__row row h-100">
                {innerJSX}
            </div>
        </div>
    }

    _pb_createRow(innerJSX, row, rowIdentifier) {
        // draggable target
        let columnRef = React.createRef();
        this.columnRefs.push(columnRef);

        // awful temp solution
        // todo add recursive level-free solution
        let rowKey = rowIdentifier.join();
        this.dictionary.rows[rowKey] = rowIdentifier;
        return <div className={`PageBuilder__column h-100 ${row.class}`}>
            <div data-rowkey={rowKey} ref={columnRef} title="Drag elements here"
                 className="PageBuilder__chunk-container PageBuilder__draggable-target d-flex flex-column justify-content-center align-items-center p-2 position-relative user-select-none">
                {innerJSX}
                {innerJSX.length < 1 &&
                <span
                    className={'PageBuilder__column-label d-block position-absolute theme__fixed-absolute--center-keep-width fw-bold fs-3'}>{row.class}</span>
                }
            </div>
        </div>
    }

    _pb_createChunk(jsx, chunk, chunkIdentifier) {
        if (typeof chunk === 'object' && Object.keys(chunk).length) {
            return <div className={`PageBuilder__chunk p-2 w-100 h-100`}>
                <div className="PageBuilder__chunk-inner p-3 d-flex justify-content-start align-items-center">
                    <div
                        className={'PageBuilder__chunk-name d-block p-2 pt-0 pb-0 d-flex justify-content-start align-items-center'}>
                        <div className="float-left">
                            <i className="fas fa-puzzle-piece fs-5"/>
                        </div>
                        <div className={'p-3 pt-0 pb-0 float-left'}>{chunk.props.name}</div>
                    </div>
                    <div className="PageBuilder__chunk-params">

                    </div>
                </div>
            </div>
        }
    }

    workspaceStateRoll(newHistoryCursor) {
        if (typeof this.state.history[newHistoryCursor] === 'undefined') {
            return false;
        }

        this.setState(() => ({
            page: {
                structure: {...this.state.history[newHistoryCursor].structure, content: this.state.history[newHistoryCursor].structure.content.returnSelf()},
                rendered: this.state.history[newHistoryCursor].rendered
            },
            historyCursor: newHistoryCursor
        }));
    }

    workspaceSavePage() {
        this.props.onUnloaded();
        this.pageBuilder.savePage({
            ...this.state.page.structure,
            content: this.state.page.structure.content.returnContent(),
            url: typeof this.input['pb_page_url'] !== 'undefined' ? this.input['pb_page_url'] : this.state.page.structure.url,
            title: typeof this.input['pb_page_title'] !== 'undefined' ? this.input['pb_page_title'] : this.state.page.structure.title
        }, () => {this.props.onLoaded()});
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

    onInputText(event, property) {
        // i'm 100% sure it is not good for performance
        // todo
        this.setState(() => ({
            page: {
                ...this.state.page,
                structure: {
                    ...this.state.page.structure,
                    [property]: event.target.value
                }
            }
        }));
    }

    dragChunk(event, index) {
        // duplicate chunk
        let duplicatedRef = React.createRef();
        console.log(duplicatedRef);
        let duplicatedItem = React.cloneElement(this.state.chunks.rendered[index], {
            ...this.state.chunks.rendered[index].props,
            passRef: duplicatedRef,
            style: {
                ...this.state.chunks.rendered[index].props.style,
                position: 'fixed',
                top: event.clientY - event.currentTarget.offsetHeight / 2,
                left: event.clientX - event.currentTarget.offsetWidth / 2,
                zIndex: 9
            }
        });
        let duplicatedIndex = this.state.chunks.rendered.length;
        this.setState(() => (
            {
                chunks: {...this.state.chunks, rendered: [...this.state.chunks.rendered, duplicatedItem]}
            }
        ), () => {
            //
        });

        this.draggable.setInitialCoordinates({top: event.clientY, left: event.clientX}, duplicatedItem.props.style);

        // i stole this idea from https://javascript.info/mouse-drag-and-drop
        let currentTarget = null;
        this.mouseMoveCallback = (mouseMoveEvent) => {
            this.draggable.dragElement(mouseMoveEvent, (newCoordinates) => {
                this.setState(() => (
                    {
                        chunks: {
                            ...this.state.chunks,
                            rendered: this.state.chunks.rendered.map((chunk, index) => (index === duplicatedIndex ? React.cloneElement(chunk, {
                                ...chunk.props,
                                style: {
                                    ...chunk.props.style,
                                    top: newCoordinates.top,
                                    left: newCoordinates.left
                                }
                            }) : chunk))
                        }
                    }
                ));

                // hide draggable element for a tiny moment
                duplicatedRef.current.hidden = true;
                // detect element below mouse
                let potentialTarget = document.elementFromPoint(mouseMoveEvent.clientX, mouseMoveEvent.clientY);
                duplicatedRef.current.hidden = false;

                if (!potentialTarget) {
                    return false;
                }

                let newTarget = potentialTarget.closest('.PageBuilder__draggable-target');
                if (newTarget !== currentTarget) {

                    // disable highlighting for current target (if it exists)
                    if (currentTarget) {
                        currentTarget.classList.remove('target-highlighted');
                    }
                    currentTarget = newTarget;
                    // and highlight new target
                    if (currentTarget) {
                        currentTarget.classList.add('target-highlighted');
                    }
                }
            });
        };
        document.addEventListener('mousemove', this.mouseMoveCallback);
        // detect if the element above place where it should be placed

        // and don't forget to unset it
        document.addEventListener('mouseup', () => {
            document.removeEventListener('mousemove', this.mouseMoveCallback);
            this.setState(() => (
                {
                    chunks: {
                        ...this.state.chunks,
                        rendered: this.state.chunks.rendered.filter((item, index) => index !== duplicatedIndex)
                    }
                }
            ));
            if (currentTarget) {
                currentTarget.classList.remove('target-highlighted');

                // we stored rowKey into row itself
                let chunk = this.state.chunks.structure[index];
                let rowKey = currentTarget.getAttribute('data-rowkey');
                let rowIdentifier = this.dictionary.rows[rowKey];
                this.renderPage({
                    ...this.state.page.structure, content: this.state.page.structure.content.insertElementByIndexArray(rowIdentifier, chunk).returnContent()
                });

                currentTarget = null;
            }
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
                                        className="PageBuilder__elements-container p-2 pb-0 pt-3 d-flex justify-content-start align-items-center position-relative">
                                        {this.state.chunks.rendered}
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
                                    <div onClick={() => this.workspaceStateRoll(this.state.historyCursor - 1)}
                                         className={`p-0 pt-3 pb-3 theme__cursor-pointer ${this.state.historyCursor > 0 ? 'theme__link-color--hover' : 'theme__element-inactive'}`}
                                         title={'Roll back to previous state'}><i className="fas fa-undo"/><span
                                        className="p-3 pb-0 pt-0">Undo</span></div>
                                    <div onClick={() => this.workspaceStateRoll(this.state.historyCursor + 1)}
                                         className={`p-3 theme__cursor-pointer ${this.state.historyCursor < this.state.history.length - 1 ? 'theme__link-color--hover' : 'theme__element-inactive'}`}
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
                                    <div onClick={this.workspaceSavePage.bind(this)}
                                         className="theme__flex-basis-0 text-center p-2 theme__cursor-pointer theme__background-color--accent-soft theme__background-color--accent-soft--hover theme__background-color--accent-soft--active"
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
                                {Object.keys(this.state.page.rendered).length &&
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
                                               value={this.state.page.structure.url}
                                               name={'pb_page_url'} id={'pb_page_url'}
                                               onInput={(event) => this.onInputText(event, 'url')}/>
                                    </div>
                                    <div
                                        className="PageBuilder__sidebar-section__item p-2 d-flex justify-content-start align-items-center">
                                        <input title={'Page title'}
                                               className={'w-100 theme__border theme__border-color d-block p-2 theme__background-color3 theme__text-color'}
                                               type={'text'} minLength={8} maxLength={60} placeholder={'Page title...'}
                                               value={this.state.page.structure.title}
                                               name={'pb_page_title'} id={'pb_page_title'}
                                               onInput={(event) => this.onInputText(event, 'title')}/>
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