import { ThunkAction, ThunkDispatch } from 'redux-thunk'

import * as api from './api'
import { AppActionTypes, AppAction } from './actions'
import { AppState, InteractorI, Mature, Sequences, Alignment } from './types'

export const selectMethod = (psimi_id: string): ThunkAction<Promise<void>, {}, {}, AppAction> => {
    return async (dispatch: ThunkDispatch<{}, {}, AppAction>): Promise<void> => {
        const f = () => {
            const method = api.methods.select(psimi_id).read()

            dispatch({ type: AppActionTypes.SELECT_METHOD, method: method })
        }

        try { f() }

        catch (promise) { promise.then(f) }
    }
}

export const unselectMethod = () => ({
    type: AppActionTypes.UNSELECT_METHOD,
})

export const selectProtein = (i: InteractorI, accession: string): ThunkAction<Promise<void>, {}, {}, AppAction> => {
    return async (dispatch: ThunkDispatch<{}, {}, AppAction>): Promise<void> => {
        const f = () => {
            const protein = api.proteins.select(accession).read()

            dispatch({ i: i, type: AppActionTypes.SELECT_PROTEIN, protein: protein })
        }

        try { f() }

        catch (promise) { promise.then(f) }
    }
}

export const unselectProtein = (i: InteractorI): AppAction => ({
    i: i,
    type: AppActionTypes.UNSELECT_PROTEIN,
})

export const editMature = (i: InteractorI): AppAction => ({
    i: i,
    type: AppActionTypes.EDIT_MATURE,
})

export const updateMature = (i: InteractorI, mature: Mature): AppAction => ({
    i: i,
    type: AppActionTypes.UPDATE_MATURE,
    mature: mature,
})

export const fireAlignment = (i: InteractorI, query: string, sequences: Sequences): ThunkAction<Promise<void>, {}, {}, AppAction> => {
    return async (dispatch: ThunkDispatch<{}, {}, AppAction>): Promise<void> => {
        dispatch({ i: i, type: AppActionTypes.FIRE_ALIGNMENT })

        api.alignment(query, sequences).then(alignment => dispatch({
            i: i,
            type: AppActionTypes.SHOW_ALIGNMENT,
            alignment: alignment
        }))
    }
}

export const addAlignment = (i: InteractorI, alignment: Alignment): AppAction => ({
    i: i,
    type: AppActionTypes.ADD_ALIGNMENT,
    alignment: alignment,
})

export const removeAlignment = (i: InteractorI, index: number): AppAction => ({
    i: i,
    type: AppActionTypes.REMOVE_ALIGNMENT,
    index: index,
})

export const cancelAlignment = (i: InteractorI): AppAction => ({
    i: i,
    type: AppActionTypes.CANCEL_ALIGNMENT,
})

export const fireSave = (run_id: number, pmid: number): ThunkAction<Promise<void>, {}, {}, AppAction> => {
    return async (dispatch: ThunkDispatch<{}, {}, AppAction>, getState: any): Promise<void> => {
        const state: AppState = getState()

        dispatch({ type: AppActionTypes.FIRE_SAVE })

        api.save(run_id, pmid, state.description).then(json => dispatch({
            type: AppActionTypes.SHOW_FEEDBACK,
            success: json.success,
            errors: json.errors,
        }))
    }
}

export const resetForm = (): ThunkAction<Promise<void>, {}, {}, AppAction> => {
    return async (dispatch: ThunkDispatch<{}, {}, AppAction>): Promise<void> => {
        dispatch({ type: AppActionTypes.RESET_FORM })
        dispatch({ i: 1, type: AppActionTypes.RESET_INTERACTOR })
        dispatch({ i: 2, type: AppActionTypes.RESET_INTERACTOR })
    }
}
