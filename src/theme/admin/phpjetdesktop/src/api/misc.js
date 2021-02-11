import {fetch2} from "../tools/fetch2";
import {setMisc} from "../actions/misc";

export const fetchMisc = () => (
    dispatch => (
        fetch2(globalSystemRootURL + '/misc', {}, result => dispatch(setMisc(result.data.misc)))
    )
);