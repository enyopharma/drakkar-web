import { ThunkAction, ThunkDispatch } from 'redux-thunk'

import { AppState, Method, InteractorI, Protein, Mature, Sequences, Alignment } from '../types'

import * as api from '../api'

// action types
export enum AppActionTypes {
    UPDATE_METHOD_QUERY,
    SELECT_METHOD,
    UNSELECT_METHOD,
    UPDATE_PROTEIN_QUERY,
    SELECT_PROTEIN,
    UNSELECT_PROTEIN,
    EDIT_MATURE,
    UPDATE_MATURE,
    UPDATE_ALIGNMENT_QUERY,
    SHOW_ALIGNMENT,
    FIRE_ALIGNMENT,
    ADD_ALIGNMENT,
    REMOVE_ALIGNMENT,
    CANCEL_ALIGNMENT,
    RESET_INTERACTOR,
    FIRE_SAVE,
    SHOW_FEEDBACK,
    RESET_FORM,
}

// actions
export type AppAction =
    | UpdateMethodQueryAction
    | SelectMethodAction
    | UnselectMethodAction
    | InteractorAction
    | FireSaveAction
    | ShowFeedbackAction
    | ResetFormAction

export type InteractorAction =
    | UpdateProteinQueryAction
    | SelectProteinAction
    | UnselectProteinAction
    | EditMatureAction
    | UpdateMatureAction
    | UpdateAlignmentQueryAction
    | FireAlignmentAction
    | ShowAlignmentAction
    | AddAlignmentAction
    | RemoveAlignmentAction
    | CancelAlignmentAction
    | ResetInteractorAction

type UpdateMethodQueryAction = {
    type: typeof AppActionTypes.UPDATE_METHOD_QUERY,
    query: string,
}

type SelectMethodAction = {
    type: typeof AppActionTypes.SELECT_METHOD,
    method: Method,
}

type UnselectMethodAction = {
    type: typeof AppActionTypes.UNSELECT_METHOD,
}

type UpdateProteinQueryAction = {
    i: InteractorI,
    type: AppActionTypes.UPDATE_PROTEIN_QUERY,
    query: string,
}

type SelectProteinAction = {
    i: InteractorI,
    type: AppActionTypes.SELECT_PROTEIN,
    protein: Protein,
}

type UnselectProteinAction = {
    i: InteractorI,
    type: AppActionTypes.UNSELECT_PROTEIN,
}

type EditMatureAction = {
    i: InteractorI,
    type: AppActionTypes.EDIT_MATURE,
}

type UpdateMatureAction = {
    i: InteractorI,
    type: AppActionTypes.UPDATE_MATURE,
    mature: Mature,
}

type UpdateAlignmentQueryAction = {
    i: InteractorI,
    type: AppActionTypes.UPDATE_ALIGNMENT_QUERY,
    query: string,
}

type FireAlignmentAction = {
    i: InteractorI,
    type: AppActionTypes.FIRE_ALIGNMENT,
}

type ShowAlignmentAction = {
    i: InteractorI,
    type: AppActionTypes.SHOW_ALIGNMENT,
    alignment: Alignment,
}

type AddAlignmentAction = {
    i: InteractorI,
    type: AppActionTypes.ADD_ALIGNMENT,
    alignment: Alignment,
}

type RemoveAlignmentAction = {
    i: InteractorI,
    type: AppActionTypes.REMOVE_ALIGNMENT,
    index: number,
}

type CancelAlignmentAction = {
    i: InteractorI,
    type: AppActionTypes.CANCEL_ALIGNMENT
}

type ResetInteractorAction = {
    i: InteractorI,
    type: AppActionTypes.RESET_INTERACTOR,
}

type FireSaveAction = {
    type: AppActionTypes.FIRE_SAVE,
}

type ShowFeedbackAction = {
    type: AppActionTypes.SHOW_FEEDBACK,
    success: boolean,
    errors: string[],
}

type ResetFormAction = {
    type: AppActionTypes.RESET_FORM,
}

// Type guard for InteractorAction
export const isInteractorAction = (action: AppAction): action is InteractorAction => {
    return (action as InteractorAction).i !== undefined
}

// creators
export const updateMethodQuery = (query: string): UpdateMethodQueryAction => ({
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

export const updateProteinQuery = (i: InteractorI, query: string): UpdateProteinQueryAction => ({
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

export const unselectProtein = (i: InteractorI): UnselectProteinAction => ({
    i: i,
    type: AppActionTypes.UNSELECT_PROTEIN,
})

export const editMature = (i: InteractorI): EditMatureAction => ({
    i: i,
    type: AppActionTypes.EDIT_MATURE,
})

export const updateMature = (i: InteractorI, mature: Mature): UpdateMatureAction => ({
    i: i,
    type: AppActionTypes.UPDATE_MATURE,
    mature: mature,
})

export const updateAlignmentQuery = (i: InteractorI, query: string): UpdateAlignmentQueryAction => ({
    i: i,
    type: AppActionTypes.UPDATE_ALIGNMENT_QUERY,
    query: query,
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

export const addAlignment = (i: InteractorI, alignment: Alignment): AddAlignmentAction => ({
    i: i,
    type: AppActionTypes.ADD_ALIGNMENT,
    alignment: alignment,
})

export const removeAlignment = (i: InteractorI, index: number): RemoveAlignmentAction => ({
    i: i,
    type: AppActionTypes.REMOVE_ALIGNMENT,
    index: index,
})

export const cancelAlignment = (i: InteractorI): CancelAlignmentAction => ({
    i: i,
    type: AppActionTypes.CANCEL_ALIGNMENT,
})

export const fireSave = (run_id: number, pmid: number): ThunkAction<Promise<void>, {}, {}, AppAction> => {
    return async (dispatch: ThunkDispatch<{}, {}, AppAction>, getState: () => AppState): Promise<void> => {
        dispatch({ type: AppActionTypes.FIRE_SAVE })

        api.save(run_id, pmid, getState()).then(json => dispatch({
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
