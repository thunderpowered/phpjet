import React from 'react';
import {connect} from 'react-redux';
import Background from "./Background";
import {fetchWallpaper} from "../../api/background";

class BackgroundContainer extends React.Component {
    componentDidMount() {
        return this.fetchWallpaper();
    }
    fetchWallpaper() {
        return this.props.dispatch(fetchWallpaper());
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