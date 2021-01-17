import React, {Component} from 'react';
import {PageBuilder} from "../../../../../../classes/PageBuilder";
import {Draggable} from "../../../../../../helpers/Draggable";
import Tab from 'react-bootstrap/Tab'
import Tabs from 'react-bootstrap/Tabs';
import {Chunk} from "./PageBuilderElements/Chunk";
import {DefinitelyNotATree} from "../../../../../../structures/DefinitelyNotATree";
import {Formatter} from "../../../../../../helpers/Formatter";

// MAIN PAGE BUILDER COMPONENT
// VERSION 1
export class WindowPageBuilder_v1 extends Component {
    constructor() {
        super();
        this.pageBuilder = new PageBuilder();
        this.draggable = new Draggable();
        this.formatter = new Formatter();
        this.state = {
            // edit|create
            mode: '',
            // all available chunks
            chunks: {
                // js-objects representing chunk structure (in terms of PageBuilder)
                structure: [],
                // react-objects needed for display info and generate events
                ready: [],
                // array of components that already placed into the page
                placed: {}
            },
            // rendered page (contains jsx array)
            page: {
                // js-object represents the page structure (including all content-json)
                structure: {},
                // array of processed React components (see recreatePageByArray function)
                ready: {}
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

        this.objectPrototypeMapDefault = Object.prototype.map;
        this.objectPrototypeFilterDefault = Object.prototype.filter;
    }

    componentDidMount() {
        let setMode = '';
        if (typeof this.props.windowData !== 'undefined' && typeof this.props.windowData.pageID !== 'undefined' && this.props.windowData.pageID) {
            this.pageBuilder.loadPage(this.props.windowData.pageID, this.renderPage.bind(this));
            setMode = 'edit';
        } else {
            setMode = 'create';
        }

        this.setState(() => ({
            mode: setMode
        }), () => this.pageBuilder.loadPageBuilderData(this.savePageBuilderData.bind(this)));
    }

    savePageBuilderData(pageBuilderData) {
        this.setState(() => ({
            chunks: {
                ...this.state.chunks,
                structure: pageBuilderData.chunks,
                ready: pageBuilderData.chunks.map((chunk, index) => (
                    <Chunk index={index}
                           name={chunk.props.name}
                           coordinates={{top: 0, left: 0}}
                           onDrugChunk={this.dragChunk.bind(this)}/>))
            },
            templates: pageBuilderData.templates
        }), () => {
            if (this.state.mode === 'create') {
                this.loadTemplate();
            }
        });
    }

    loadTemplate(templateID = 0) {
        // todo save default template
        let template = this.state.templates[templateID];
        if (typeof template === 'undefined') {
            Msg.error('Template does not exist');
            return false;
        }

        this.renderPage({...template, id: 0});
    }

    renderPage(currentPage) {
        let pageContent = new DefinitelyNotATree(currentPage.content);
        let pageRendered = this.recreatePageByArray2(pageContent.root);

        this.setState(() => ({
            page: {
                structure: {...currentPage, content: pageContent},
                ready: pageRendered
            },
            history: [...this.state.history.splice(0, this.state.historyCursor + 1), {
                structure: {...currentPage, content: pageContent.returnSelf()},
                ready: pageRendered
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
            page: {...this.state.page, ready: {content: renderedPage}},
            history: [...this.state.history.splice(0, this.state.historyCursor + 1), {
                ...this.state.page,
                ready: {content: renderedPage}
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
        let chunkIndex = chunkIdentifier.join("");
        if (typeof this.state.chunks.placed[chunkIndex] !== 'undefined') {
            return this.state.chunks.placed[chunkIndex];
        }
        let chunkRef = React.createRef();
        let chunkComponent = <Chunk index={chunkIndex}
                                    structureIndex={0}
                                    name={chunk.props.name}
                                    coordinates={{top: 0, left: 0}}
                                    inPlace={true}
                                    identifier={chunkIdentifier}
                                    ref={chunkRef}
                                    onDrugChunk={(event) => this.dragChunk(event, chunkIndex, false, false)}/>;
        this.setState(() => ({
                chunks: {
                    ...this.state.chunks,
                    placed: {
                        ...this.state.chunks.placed,
                        [chunkIndex]: chunkComponent
                    }
                }
        }));
        return chunkComponent;

        // old but will be deleted later
        if (typeof chunk === 'object' && Object.keys(chunk).length) {
            return <div className={`PageBuilder__chunk p-2 w-100 h-100 theme__cursor-pointer`}>
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
                ready: this.state.history[newHistoryCursor].ready
            },
            historyCursor: newHistoryCursor
        }));
    }

    workspaceSavePage(callback) {
        // this.props.onUnloaded();
        this.pageBuilder.savePage({
            ...this.state.page.structure,
            content: this.state.page.structure.content.returnContent()
        }, (resultID) => {
            this.setState(() => ({
                page: {
                    ...this.state.page,
                    structure: {
                        ...this.state.page.structure,
                        id: resultID
                    }
                }
            }));
        });
    }

    workspaceVisitPage() {
        let pageURL = this.formatter.formatRelativeURL(this.state.page.structure.url);
        pageURL = globalSystemHost + pageURL;
        let win = window.open(pageURL, '_blank');
        win.focus();
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
        if (typeof event.target === 'undefined' || typeof event.target.value === 'undefined') {
            return false;
        }
        let value = event.target.value;
        // todo make it more flexible
        // this is the question about basic automatic frontend validation (see todoist)
        if (property === 'url') {
            value = this.formatter.formatRelativeURL(value);
        }

        this.setState(() => ({
            page: {
                ...this.state.page,
                structure: {
                    ...this.state.page.structure,
                    [property]: value
                }
            }
        }));
    }

    dragChunk(event, index, ready = true, cloneOnDrugging = true, targetContainerSelector = '.PageBuilder__draggable-target') {
        // this works fine for now
        // but in the future it'd be greater to make it more flexible

        // since js does not have 'map' and 'filter' functions for Objects, let's write them!
        // upd. seems like DataTables also implements Object.prototype methods, and it causes crash of entire application
        // so it is important to restore 'default' values when we don't need em
        Object.prototype.map = function(callback) {
            let entries = Object.entries(this);
            let map = entries.map(([index, item]) => [index, callback(item, index)]);
            return Object.fromEntries(map.filter(entry => entry));
        };
        Object.prototype.filter = function(callback) {
            let entries = Object.entries(this);
            let filtered = entries.filter(([index, item]) => {
                let callbackResult = callback(item, index);
                if (!callbackResult) {
                    return false;
                }
                return [index, item];
            });
            return Object.fromEntries(filtered.filter(entry => entry));
        };

        // contains chunks that is ready to be placed (most likely there are in the top element list)
        let chunkArrayKey = 'ready';
        if (!ready) {
            // contains chunks already placed into page
            chunkArrayKey = 'placed';
        }

        let duplicatedRef = React.createRef();
        if (!cloneOnDrugging) {
            duplicatedRef = this.state.chunks[chunkArrayKey][index].ref;
        }

        // when user tries to grab chunk from elements menu, we should copy it, since each chunk can be reused infinite amount of times
        // but if user grabs chunk from the page we shouldn't copy it, just put in the different place or delete
        let duplicatedItem = React.cloneElement(this.state.chunks[chunkArrayKey][index], {
            ...this.state.chunks[chunkArrayKey][index].props,
            passRef: duplicatedRef,
            style: {
                ...this.state.chunks[chunkArrayKey][index].props.style,
                position: 'fixed',
                top: event.clientY - event.currentTarget.offsetHeight / 2,
                left: event.clientX - event.currentTarget.offsetWidth / 2,
                zIndex: 11
            }
        });

        let duplicatedIndex = index;
        let chunkStructureIdentifier = index;
        if (!ready) {
            chunkStructureIdentifier = this.state.chunks[chunkArrayKey][index].props.identifier;
        }

        if (cloneOnDrugging && ready) {
            duplicatedIndex = this.state.chunks[chunkArrayKey].length;
            this.setState(() => (
                {
                    chunks: {...this.state.chunks, [chunkArrayKey]: [...this.state.chunks[chunkArrayKey], duplicatedItem]}
                }
            ));
        } else if (!cloneOnDrugging && !ready) {
            console.log('wood-de-doo');
            this.setState(() => (
                {
                    chunks: {...this.state.chunks, [chunkArrayKey]: this.state.chunks[chunkArrayKey].map((chunk, _index) => _index === duplicatedIndex ? duplicatedItem : chunk)}
                }
            ))
        } else {
            // giving up
            Msg.error('Impossible to accomplish the task');
            return false;
        }

        this.draggable.setInitialCoordinates({top: event.clientY, left: event.clientX}, duplicatedItem.props.style);

        // i stole this idea from https://javascript.info/mouse-drag-and-drop
        let currentTarget = null;
        this.mouseMoveCallback = (mouseMoveEvent) => {
            this.draggable.dragElement(mouseMoveEvent, (newCoordinates) => {
                this.setState(() => (
                    {
                        chunks: {
                            ...this.state.chunks,
                            // i've no idea, but if i put this array to variable, it won't work
                            [chunkArrayKey]: this.state.chunks[chunkArrayKey].map((chunk, _index) => _index === duplicatedIndex ? React.cloneElement(chunk, {
                                ...chunk.props,
                                style: {
                                    ...chunk.props.style,
                                    top: newCoordinates.top,
                                    left: newCoordinates.left
                                }
                            }) : chunk)
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

                let newTarget = potentialTarget.closest(targetContainerSelector);
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

            // temporary solution, i swear i'll bring better solution very soon
            // todo
            if (!ready) {
                this.renderPage({
                    ...this.state.page.structure, content: this.state.page.structure.content.returnContent()
                });
            }
        };
        document.addEventListener('mousemove', this.mouseMoveCallback);
        // detect if the element above place where it should be placed

        // and don't forget to unset it
        this.mouseUpCallback = () => {
            document.removeEventListener('mousemove', this.mouseMoveCallback);
            // delete visible draggable element
            this.setState(() => (
                {
                    chunks: {
                        ...this.state.chunks,
                        [chunkArrayKey]: this.state.chunks[chunkArrayKey].filter((item, index) => index !== duplicatedIndex)
                    }
                }
            ), () => {
                // if the mouse is over target -> drop object into it
                if (currentTarget) {
                    currentTarget.classList.remove('target-highlighted');

                    // we stored rowKey into row itself
                    let chunk = this.state.chunks.structure[index];
                    if (!ready) {
                        chunk = this.state.page.structure.content.findByIndexArray(chunkStructureIdentifier);
                    }

                    let rowKey = currentTarget.getAttribute('data-rowkey');
                    let rowIdentifier = this.dictionary.rows[rowKey];

                    this.renderPage({
                        ...this.state.page.structure, content: this.state.page.structure.content.insertElementByIndexArray(rowIdentifier, chunk).returnContent()
                    });

                    currentTarget = null;
                }

                if (!ready) {
                    this.renderPage({
                        ...this.state.page.structure, content: this.state.page.structure.content.deleteElementByIndexArray(chunkStructureIdentifier).returnContent()
                    });
                }
            });
            document.removeEventListener('mouseup',  this.mouseUpCallback);

            Object.prototype.map = this.objectPrototypeMapDefault;
            Object.prototype.filter = this.objectPrototypeFilterDefault;
        };
        document.addEventListener('mouseup', this.mouseUpCallback);
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
                                        {this.state.chunks.ready}
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
                                    <div onClick={this.workspaceVisitPage.bind(this)}
                                         className="p-3 theme__cursor-pointer theme__link-color--hover"
                                         title={'Open a new tab and jump to the actual page'}><i className="fas fa-share-square"/><span
                                        className="p-3 pb-0 pt-0">Visit the page</span></div>
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
                                {Object.keys(this.state.page.ready).length &&
                                this.state.page.ready
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