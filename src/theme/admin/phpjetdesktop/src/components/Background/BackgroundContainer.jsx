import React from 'react';
import {connect} from 'react-redux';
import Background from "./Background";
import {fetch2} from "../../api/fetch2";
import {changeWallpaper} from "../../actions/background";

class BackgroundContainer extends React.Component {
    componentDidMount() {
        return this.fetchWallpaper();
    }
    fetchWallpaper() {
        const {dispatch} = this.props;
        // okay, i got a better idea
        // not i understand why i created the API folder
        return fetch2(this.props.urlChangeWallpaper, {}, result => dispatch(changeWallpaper(result.wallpaper)));
    }
    render() {
        return (
            <Background onChangeWallpaper={this.onChangeWallpaper.bind(this)} wallpaper={this.props.wallpaper}/>
        )
    }
}

const mapStateToProps = state => ({
    wallpaper: state.background.wallpaper,
    urlChangeWallpaper: state.auth.url.changeWallpaper
});

export default connect(mapStateToProps)(BackgroundContainer)