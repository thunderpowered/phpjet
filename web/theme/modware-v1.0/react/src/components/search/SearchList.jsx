import React, {Component} from 'react';

export class SearchList extends Component {
    maxNameStringLength = 65;

    render() {
        // just a fancy way to get part of object in JS :)
        let {list, show} = this.props;

        // if no items to render -> just hide the block without any messages
        let display = 'block';
        if (typeof list === 'undefined' || list.length === 0 || !show) {
            display = 'none';
        }

        return <div style={{'display': display}} className={'header__search-list-wrapper theme__background-color3 theme__box-shadow'}>
            <ul className="p-0 header__search-list d-flex flex-column m-0 mb-3">
                {
                    list.map((item, index) => {
                        if (typeof item.name === 'string' && item.name.length > this.maxNameStringLength) {
                            // yep, just crop it
                            item.name = item.name.substring(0, this.maxNameStringLength - 3) + '...';
                        }
                        return <li className={'header__search-list d-block p-4 pb-0'}>
                            <a className={'theme__link-color theme__link-color--hover fw-regular fs-10'}
                               href={globalSystemHost + '/' + item.type + '/' + item.url}>[{item.typeRU}] {item.name}</a>
                        </li>
                    })
                }
            </ul>
        </div>
    }
}