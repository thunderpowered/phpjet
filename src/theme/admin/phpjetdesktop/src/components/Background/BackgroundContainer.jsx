import React from 'react';
import {connect} from 'react-redux';
import Background from "./Background";
import {fetchWallpaper} from "../../api/background";

class BackgroundContainer extends React.Component {
    componentDidMount() {
        this.props.dispatch(fetchWallpaper())
    }
    onChangeWallpaper() {

    }
    render() {
        const {wallpaper} = this.props;
        return (
            <Background onChangeWallpaper={this.onChangeWallpaper.bind(this)} wallpaper={wallpaper}/>
        )
    }
}

const mapStateToProps = state => ({
    wallpaper: state.background.wallpaper
});

export default connect(mapStateToProps)(BackgroundContainer)