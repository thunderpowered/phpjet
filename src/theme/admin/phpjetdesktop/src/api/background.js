import {setWallpaper} from "../actions/background";
import api from "../tools/api";

export const fetchWallpaper = adminID => (
    dispatch => (
        api.get(`admin/${adminID}/settings/wallpaper`, {}, result => dispatch(setWallpaper(result.data.wallpaper)))
    )
);

export const changeWallpaper = (adminID, queryParams) => (
    dispatch => (
        api.file(`admin/${adminID}/settings/wallpaper`, {queryParams: queryParams}, result => dispatch(setWallpaper(result.data.wallpaper)))
    )
);