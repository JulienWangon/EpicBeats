import instanceAxios from '../../_utils/axios';


//OBTENIR LA LISTE DES INSTRUMENTALES
export const fetchInstrumentalsList = async () => {

  try {
      const response = await instanceAxios.get('/instrumentals');
      if(response.data && response.data.status === 'success') {
          return response.data.data;
      } else {
          throw new Error(response.data.message || "Données reçues non valide ou erreur de requête.")
      }
  } catch (error) {
      const errorMessage = error.response.data.errorMessage;
      throw new Error(errorMessage);
  }
}