import {setWallpaper} from "../actions/background";
import {fetch2, fetch2file} from "../tools/fetch2";

export const fetchWallpaper = () => (
    dispatch => (
        fetch2(globalSystemRootURL + '/misc/getWallpaper', {}, result => dispatch(setWallpaper(result.data.wallpaper)))
    )
);

export const changeWallpaper = queryParams => (
    dispatch => (
        fetch2file(globalSystemRootURL + '/misc/setWallpaper', {queryParams: queryParams}, result => dispatch(setWallpaper(result.data.wallpaper)))
    )
);