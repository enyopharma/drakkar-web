export type DescriptionType = 'hh' | 'vh'

export type SearchResult = {
    value: string,
    label: string,
}

export type MethodSearchResult = {
    psimi_id: string,
    name: string,
}

export type Method = {
    psimi_id: string,
    name: string,
}

export type InteractorI = 1 | 2

export type ProteinType = 'h' | 'v'

export type ProteinSearchResult = {
    type: ProteinType,
    accession: string,
    taxon: string,
    name: string,
    description: string,
}

export type Protein = {
    type: ProteinType,
    accession: string,
    taxon: string,
    name: string,
    description: string,
    sequence: string,
    isoforms: Isoform[],
    chains: Chain[],
    domains: Domain[],
    matures: Mature[],
}

export type Isoform = {
    accession: string,
    sequence: string,
    is_canonical: boolean,
}

export type Chain = {
    key: string,
    description: string,
    start: number,
    stop: number,
}

export type Domain = {
    key: string,
    description: string,
    start: number,
    stop: number,
}

export type Mature = {
    name: string,
    start: number,
    stop: number,
}

export type Sequences = Record<string, string>

export type Coordinates = Record<string, { start: number, stop: number, width: number }>

export type Alignment = {
    sequence: string,
    isoforms: Array<{
        accession: string,
        occurrences: Array<{
            start: number,
            stop: number,
            identity: number,
        }>
    }>,
}

export type Feedback = {
    success: boolean,
    errors: string[],
}

export type AppState = {
    method: MethodState,
    interactor1: InteractorState,
    interactor2: InteractorState,
    ui: AppUiState,
}

export type AppUiState = {
    saving: boolean,
    feedback: Feedback
}

export type MethodState = {
    query: string,
    psimi_id: string,
}

export type ProteinState = {
    query: string,
    accession: string,
}

export type AlignmentState = {
    query: string,
    current: Alignment,
}

export type InteractorState = {
    i: InteractorI,
    protein: ProteinState,
    name: string,
    start: number,
    stop: number,
    mapping: Alignment[],
    ui: InteractorUiState,
}

export type InteractorUiState = {
    editing: boolean,
    processing: boolean,
    alignment: AlignmentState,
}
