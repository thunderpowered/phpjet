import React, {Component} from 'react';

export class WindowPageBuilder extends Component {
    componentDidMount() {
        this.props.onLoaded();
    }

    render() {
        return <div>
            Page Builder {this.props.parent}
        </div>
    }
}