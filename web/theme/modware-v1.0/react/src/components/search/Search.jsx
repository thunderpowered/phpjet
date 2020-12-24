import React, {Component} from 'react';
import {SearchList} from "./SearchList";
import {fetch2} from "../../helpers/fetch2";

export class Search extends Component {

    value;
    action;

    cache = {};

    // do not proceed search until input length reach this value
    minLengthToSearch = 2;

    opacity = {
        // when active
        defaultFocus: 1,
        // when not active
        defaultBlur: 0
    };

    url = {
        // header search
        quickSearch: '/search/AJAXQuickSearch',
        // when user clicks Enter
        basicSearch: '/search'
    };

    // React state
    state = {
        opacity: 0.8,
        showBlock: false,
        list: []
    };

    constructor() {
        super();
        this.action = globalSystemHost + this.url.basicSearch;

        // set default as initial opacity
        this.opacity.defaultBlur = this.state.opacity;
    }

    onInput(event) {
        this.value = event.target.value;
        if (typeof this.value !== 'undefined' && this.value.length >= this.minLengthToSearch) {
            return this.search(this.value);
        } else {
            this.setState(() => {
                return {list: []}
            });
        }

    }

    // main Search
    search(value) {

        // let's try to reduce number of queries
        if (this.cache[value]) {
            return this.proceedSearchResult(this.cache[value]);
        }

        return fetch2(this.url.quickSearch, {
            queryParams: {
                'searchValue': value
            }
        }, {
            onSuccess: (result) => {
                if (result && typeof result.searchResult !== 'undefined') {
                    this.cache[value] = result.searchResult;
                    this.proceedSearchResult(result.searchResult);
                } else {
                    this.setState(() => {
                        return {list: []}
                    });
                }
            }
        });
    }

    proceedSearchResult(result) {
        this.setState(() => {
            return {list: result}
        });
    }

    // some UV shit
    opacityControl(focus = false) {
        let setOpacity = 0;
        let setShowBlock = false;
        if (focus) {
            setOpacity = this.opacity.defaultFocus;
            setShowBlock = true;
        } else {
            setOpacity = this.opacity.defaultBlur;
            setShowBlock = false;
        }

        this.setState(() => {
            return {opacity: setOpacity, showBlock: setShowBlock}
        });
    }

    render() {
        return <form id={'SearchForm'} action={this.action} className={'header__search-form theme__form-format'}
                     style={{opacity: this.state.opacity}}>
            <input
                className={'p-2 header__search-input theme__text-color theme__background-color3 theme__input-bar__format theme__input-bar__border-color'}
                type={'text'}
                name={'input_header_search'}
                autoComplete={false}
                onInput={(e) => this.onInput(e)}
                onFocus={() => this.opacityControl(true)}
                onBlur={() => this.opacityControl(false)}
                placeholder={'Поиск во всех разделах...'}
            />
            <div className={'theme__fa-wrapper header__search-icon-wrapper'}>
                <i className="fas fa-search theme__text-color"></i>
            </div>

            <SearchList list={this.state.list} show={this.state.showBlock}/>
        </form>
    }
}