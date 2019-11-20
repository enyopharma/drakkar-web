export type DescriptionType = 'hh' | 'vh'

export type ProteinType = 'h' | 'v'

export type InteractorI = 1 | 2

export type AppState = {
    description: Description,
    uinterface: UInterface,
}

export type Description = {
    method: {
        psimi_id: string,
    },
    interactor1: Interactor,
    interactor2: Interactor,
}

export type Interactor = {
    protein: {
        accession: string,
    },
    name: string,
    start: number,
    stop: number,
    mapping: Alignment[],
}

export type UInterface = {
    method: {
        query: string,
    }
    interactor1: InteractorInterface,
    interactor2: InteractorInterface,
    saving: boolean,
    feedback: Feedback
}

export type InteractorInterface = {
    protein: {
        query: string,
    }
    editing: boolean,
    processing: boolean,
    alignment: {
        query: string,
        current: Alignment,
    }
}

export type SearchResult = {
    value: string,
    label: string,
}

export type Method = {
    psimi_id: string,
    name: string,
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
