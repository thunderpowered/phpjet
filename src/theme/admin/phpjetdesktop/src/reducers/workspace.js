import {PANEL_MODE_WINDOW} from "../constants/Mode";
import {OPEN_NEW_WINDOW, SET_PANEL_MODE} from "../actions/workspace";

const initialState = {
    mode: PANEL_MODE_WINDOW
};

const workspace = (state = initialState, action) => {
      switch (action.type) {
          case SET_PANEL_MODE:
              return {
                  ...state,
                  mode: action.mode
              };

          default:
              return state;
      }
};

export default workspace