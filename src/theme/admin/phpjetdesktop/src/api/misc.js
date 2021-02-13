import api from "../tools/api";
import {setMisc} from "../actions/misc";

export const fetchMisc = () => (
    dispatch => (
        api.get('misc', {}, result => dispatch(setMisc(result.data.misc)))
    )
);