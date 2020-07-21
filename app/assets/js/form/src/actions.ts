import { Method, InteractorI, Protein, Mature, Alignment } from './types'

export enum AppActionTypes {
    SELECT_METHOD,
    UNSELECT_METHOD,
    INIT_PROTEIN,
    SELECT_PROTEIN,
    UNSELECT_PROTEIN,
    EDIT_MATURE,
    UPDATE_MATURE,
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

export type AppAction =
    | SelectMethodAction
    | UnselectMethodAction
    | InteractorAction
    | FireSaveAction
    | ShowFeedbackAction
    | ResetFormAction

export type InteractorAction =
    | SelectProteinAction
    | UnselectProteinAction
    | EditMatureAction
    | UpdateMatureAction
    | FireAlignmentAction
    | ShowAlignmentAction
    | AddAlignmentAction
    | RemoveAlignmentAction
    | CancelAlignmentAction
    | ResetInteractorAction

export const isInteractorAction = (action: AppAction): action is InteractorAction => {
    return (action as InteractorAction).i !== undefined
}

type SelectMethodAction = {
    type: typeof AppActionTypes.SELECT_METHOD,
    method: Method,
}

type UnselectMethodAction = {
    type: typeof AppActionTypes.UNSELECT_METHOD,
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
