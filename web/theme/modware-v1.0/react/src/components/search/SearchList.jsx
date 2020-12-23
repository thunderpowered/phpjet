import React, {Component} from 'react';

export class SearchList extends Component {
    render() {
        let {list} = this.props;

        let display = 'block';
        if (typeof list === 'undefined' || list.length === 0) {
            display = 'none';
        }

        return <div style={{'display': display}} className={'header__search-list-wrapper'}>
            <ul className="header__search-list">

            </ul>
        </div>
    }
}