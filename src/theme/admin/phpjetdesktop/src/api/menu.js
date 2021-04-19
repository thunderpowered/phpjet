import api from "../tools/api";
import {setMenuList} from "../actions/menu";

export const fetchMenu = () => (
    dispatch => (
        api.get('record/*', {queryParams: {'mode': 'only_title'}}, result => (
            dispatch(setMenuList(result.data.records))
        ))
    )
);