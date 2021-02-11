import {SET_WALLPAPER} from "../actions/background";

const initialState = {
    wallpaper: ''
};

const background = (state = initialState, action) => {
      switch (action.type) {
          case SET_WALLPAPER:
              return {
                  wallpaper: action.wallpaper
              };
          default:
              return state;
      }
};

export default background