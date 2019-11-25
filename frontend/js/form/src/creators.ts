import { ThunkAction, ThunkDispatch } from 'redux-thunk'

import * as api from '../api'
import { AppState } from './state'
import { AppActionTypes, AppAction } from './actions'
import { Method, InteractorI, Protein, Mature, Sequences, Alignment } from './types'

export const updateMethodQuery = (query: string): AppAction => ({
    type: AppActionTypes.UPDATE_METHOD_QUERY,
    query: query,
})

export const selectMethod = (psimi_id: string): ThunkAction<Promise<void>, {}, {}, AppAction> => {
    return async (dispatch: ThunkDispatch<{}, {}, AppAction>): Promise<void> => {
        api.methods.select(psimi_id).then(method => dispatch({
            type: AppActionTypes.SELECT_METHOD,
            method: method,
        }))
    }
}

export const unselectMethod = () => ({
    type: AppActionTypes.UNSELECT_METHOD,
})

export const updateProteinQuery = (i: InteractorI, query: string): AppAction => ({
    i: i,
    type: AppActionTypes.UPDATE_PROTEIN_QUERY,
    query: query,
})

export const selectProtein = (i: InteractorI, accession: string): ThunkAction<Promise<void>, {}, {}, AppAction> => {
    return async (dispatch: ThunkDispatch<{}, {}, AppAction>): Promise<void> => {
        api.proteins.select(accession).then(protein => dispatch({
            i: i,
            type: AppActionTypes.SELECT_PROTEIN,
            protein: protein,
        }))
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

export const initForm = (): ThunkAction<Promise<void>, {}, {}, AppAction> => {
    return async (dispatch: ThunkDispatch<{}, {}, AppAction>, getState: any): Promise<void> => {
        const state: AppState = getState()

        const psimi_id = state.description.method.psimi_id
        const accession1 = state.description.interactor1.protein.accession
        const accession2 = state.description.interactor2.protein.accession

        const promises: [Promise<Method | null>, Promise<Protein | null>, Promise<Protein | null>] = [
            new Promise(resolve => psimi_id == null ? resolve(null) : api.methods.select(psimi_id).then(resolve)),
            new Promise(resolve => accession1 == null ? resolve(null) : api.proteins.select(accession1).then(resolve)),
            new Promise(resolve => accession2 == null ? resolve(null) : api.proteins.select(accession2).then(resolve)),
        ]

        Promise.all(promises).then(([method, protein1, protein2]) => {
            dispatch({ type: AppActionTypes.INIT_METHOD, method: method })
            dispatch({ i: 1, type: AppActionTypes.INIT_PROTEIN, protein: protein1 })
            dispatch({ i: 2, type: AppActionTypes.INIT_PROTEIN, protein: protein2 })
            dispatch({ type: AppActionTypes.SHOW_FORM })
        })
    }
}

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
