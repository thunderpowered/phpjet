import {setWallpaper} from "../actions/background";
import {fetch2} from "../tools/fetch2";

export const fetchWallpaper = () => (
    dispatch => (
        fetch2(globalSystemRootURL + '/misc/getWallpaper', {}, result => dispatch(setWallpaper(result.data.wallpaper)))
    )
);