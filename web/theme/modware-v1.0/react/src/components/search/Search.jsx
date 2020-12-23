import React, {Component} from 'react';
import {Token} from "../../helpers/token";
import {SearchList} from "./SearchList";

export class Search extends Component {

    value;
    action;
    token;
    minLengthToSearch;

    opacityDefaultFocus;
    opacityDefaultBlur;

    state = {
        opacity: 0.8,
        list: []
    };

    constructor() {
        super();
        this.action = globalSystemHost + '/search/fastSearch';
        this.token = Token;
        this.minLengthToSearch = 5;

        this.opacityDefaultFocus = 1;
        this.opacityDefaultBlur = this.state.opacity;
    }

    onInput(event) {
        this.value = event.target.value;
        if (typeof this.value !== 'undefined' && this.value.length >= this.minLengthToSearch) {
            this.search(this.value);
        }
    }

    // main Search
    search(value) {
        console.log(value);
        // @todo do something
    }

    // some UV shit
    opacityControl(focus = false) {
        let setOpacity = focus ? this.opacityDefaultFocus : this.opacityDefaultBlur;
        this.setState(() => {
            return {opacity: setOpacity}
        });
    }

    render() {
        return <form id={'SearchForm'} action={this.action} className={'header__search-form theme__form-format'}
                     style={{opacity: this.state.opacity}}>
            <input
                className={'p-1 header__search-input theme__text-color theme__background-color theme__input-bar__format theme__input-bar__border-color'}
                type={'text'}
                name={'input_header_search'}
                autoComplete={false}
                value={this.value}
                onInput={(e) => this.onInput(e)}
                onFocus={() => this.opacityControl(true)}
                onBlur={() => this.opacityControl(false)}
                placeholder={'Поиск во всех разделах...'}
            />
            <div className={'theme__fa-wrapper header__search-icon-wrapper'}>
                <i className="fas fa-search theme__text-color"></i>
            </div>

            <SearchList list={this.state.list}/>
        </form>
    }
}