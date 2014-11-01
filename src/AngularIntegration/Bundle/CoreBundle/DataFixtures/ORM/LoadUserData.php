<?php
/**
 * Created by PhpStorm.
 * User: Thiago
 * Date: 18/06/14
 * Time: 16:03
 */

namespace Operadores\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\ORMException;
use AngularIntegration\Bundle\AdminBundle\Entity\Atividade;
use AngularIntegration\Bundle\AdminBundle\Entity\ConfiguracaoBlocoTemplateEscalaTurno;
use AngularIntegration\Bundle\AdminBundle\Entity\ConfiguracaoTemplateEscalaTurno;
use AngularIntegration\Bundle\AdminBundle\Entity\DataCenter;
use AngularIntegration\Bundle\AdminBundle\Entity\Bloco;
use AngularIntegration\Bundle\AdminBundle\Entity\BlocoAtividade;
use AngularIntegration\Bundle\AdminBundle\Entity\EscalaTurno;
use AngularIntegration\Bundle\AdminBundle\Entity\Resposta;
use AngularIntegration\Bundle\AdminBundle\Entity\Template;


class LoadUserData implements FixtureInterface
{
    private $manager;

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $this->createDataCenter();
        $this->createEquipamento();
        $this->createAtividade();
        $this->createBloco();
        $this->createTemplate();
        //$this->createEscalaTurno();

    }

    private function createDataCenter(){
        $objDataCenter = new DataCenter();
        $objDataCenter->nomePortugues = "Português 1";
        $objDataCenter->nomeEspanhol  = "Espanhol  1";
        $objDataCenter->descricaoPortugues = "descricao em Português 1";
        $objDataCenter->descricaoEspanhol = "descricao em Espanhol 1";

        $objDataCenter->status = true;
        $objDataCenter->validoPortugues = true;
        $objDataCenter->validoEspanhol = true;

        $objDataCenter2 = new DataCenter();
        $objDataCenter2->nomePortugues = "Português 2";
        $objDataCenter2->nomeEspanhol  = "Espanhol  2";
        $objDataCenter2->descricaoPortugues = "descricao em Português 2";
        $objDataCenter2->descricaoEspanhol = "descricao em Espanhol 2";

        $objDataCenter2->status = true;
        $objDataCenter2->validoPortugues = true;
        $objDataCenter2->validoEspanhol = true;

        $this->manager->persist($objDataCenter);
        $this->manager->persist($objDataCenter2);
        $this->manager->flush();
    }

    private function createEquipamento(){

    }

    private function createAtividade()
    {
        $objAtividade1 = new Atividade();
        $objAtividade1->descricaoPortugues = "descricao em Português 1";
        $objAtividade1->descricaoEspanhol = "descricao em Espanhol 1";
        $objAtividade1->nivelCritico = "BAIXO";
        $objAtividade1->status = true;
        $objAtividade1->tipo = "MULTI";
        $objAtividade1->validoPortugues = true;
        $objAtividade1->validoEspanhol = true;

        $objAtividade2 = new Atividade();
        $objAtividade2->descricaoPortugues = "descricao em Português 2";
        $objAtividade2->descricaoEspanhol = "descricao em Espanhol 2";
        $objAtividade2->nivelCritico = "BAIXO";
        $objAtividade2->status = true;
        $objAtividade2->tipo = "MULTI";
        $objAtividade2->validoPortugues = true;
        $objAtividade2->validoEspanhol = true;

        $objResposta1 = new Resposta();
        $objResposta1->nomePortugues = "Resposta atividade 1 em portugues";
        $objResposta1->nomeEspanhol  = "Resposta atividade 1 em espanhol 1";
        $objResposta1->atividade  = $objAtividade1;

        $objResposta2 = new Resposta();
        $objResposta2->nomePortugues = "Resposta atividade 1 em português 1";
        $objResposta2->nomeEspanhol  = "Resposta atovodade 1 em espanhol  1";
        $objResposta2->atividade  = $objAtividade1;

        $objResposta3 = new Resposta();
        $objResposta3->nomePortugues = "Resposta atividade 2 em portugues";
        $objResposta3->nomeEspanhol  = "Resposta atividade 2 em espanhol 1";
        $objResposta3->atividade  = $objAtividade2;

        $objResposta4 = new Resposta();
        $objResposta4->nomePortugues = "Resposta atividade 2 em português 1";
        $objResposta4->nomeEspanhol  = "Resposta atovodade 2 em espanhol  1";
        $objResposta4->atividade  = $objAtividade2;

        $this->manager->persist($objResposta1);
        $this->manager->persist($objResposta2);
        $this->manager->persist($objResposta3);
        $this->manager->persist($objResposta4);
        $this->manager->flush();
    }

    private function createBloco(){
        try {
            $objBloco1 = new Bloco();
            $objBloco1->tituloPortugues = "Titulo em Espanhol 1";
            $objBloco1->tituloEspanhol = "Titulo em Espanhol 1";
            $objBloco1->descricaoPortugues = "Descrição em Português 1";
            $objBloco1->descricaoEspanhol  = "Descrição em Espanhol 1";
            $objBloco1->status = true;
            $objBloco1->repetir = false;
            $objBloco1->validoPortugues = true;
            $objBloco1->validoEspanhol  = true;

            $this->manager->persist($objBloco1);

            $objBloco1->id = 1;

            $objAtividade1 = $this->manager->find( 'AngularIntegration\Bundle\AdminBundle\Entity\Atividade', 1 );
            $objAtividade2 = $this->manager->find( 'AngularIntegration\Bundle\AdminBundle\Entity\Atividade', 2 );

            $objBlocoAtividade1 = new BlocoAtividade();
            $objBlocoAtividade2 = new BlocoAtividade();

            $objBlocoAtividade1->bloco = $objBloco1;
            $objBlocoAtividade1->atividade = $objAtividade1;

            $objBlocoAtividade2->bloco = $objBloco1;
            $objBlocoAtividade2->atividade = $objAtividade2;

            $this->manager->persist($objBlocoAtividade1);
            $this->manager->persist($objBlocoAtividade2);

            $this->manager->flush();

        } catch (ORMException $e){
            throw new \Exception( $e->getMessage() );
        }

    }

    private function createTemplate(){
        $objTemplate = new Template();
        $objTemplate->nomePortugues = "Nome template Português";
        $objTemplate->nomeEspanhol  = "Nome template Espanhol";
        $objTemplate->validoPortugues = true;
        $objTemplate->validoEspanhol  = true;

        $objBloco1 = $this->manager->find( 'AngularIntegration\Bundle\AdminBundle\Entity\Bloco', 1 );
        $objTemplate->blocos[] = $objBloco1;

        $this->manager->persist($objTemplate);

        $this->manager->flush();

    }

    private function createEscalaTurno(){
        try {
            $objEscalaTurno = new EscalaTurno();

            $objEscalaTurno->tipo = "PADRAO";
            $this->manager->persist($objEscalaTurno);

            $templates = array();
            $templates[] = $this->manager->find( 'AngularIntegration\Bundle\AdminBundle\Entity\Template', 1 );

            foreach( $templates as $template ) {
                $configuracaoTemplateEscalaTurno = new ConfiguracaoTemplateEscalaTurno();
                $configuracaoTemplateEscalaTurno->escalaTurno = $objEscalaTurno;
                $configuracaoTemplateEscalaTurno->template =  $this->manager->find( 'AngularIntegration\Bundle\AdminBundle\Entity\Template', $template->id );

                $configuracaoTemplateEscalaTurno->horaInicio = new \DateTime();
                $configuracaoTemplateEscalaTurno->horaFim = new \DateTime();

                if(isset($template->blocos))
                    foreach($template->blocos as $bloco) {
                        $configuracaoBlocoTemplateEscalaTurno = new ConfiguracaoBlocoTemplateEscalaTurno();
                        $configuracaoBlocoTemplateEscalaTurno->bloco = $this->manager->find( 'AngularIntegration\Bundle\AdminBundle\Entity\Bloco', $bloco->id );
                        $configuracaoBlocoTemplateEscalaTurno->template = $configuracaoTemplateEscalaTurno->template;
                        $configuracaoBlocoTemplateEscalaTurno->escalaTurno = $objEscalaTurno;
                        $configuracaoBlocoTemplateEscalaTurno->escalaTurnoTemplate = $configuracaoTemplateEscalaTurno;

                        if( $bloco->blocoConfig->tipoHorario ) {
                            $configuracaoBlocoTemplateEscalaTurno->tipoHorario = $bloco->blocoConfig->tipoHorario;

                            if( $bloco->blocoConfig->horaExecucao )
                                $configuracaoBlocoTemplateEscalaTurno->horaExecucao = new \DateTime($bloco->blocoConfig->horaExecucao->hours);
                            else if ( $bloco->blocoConfig->intervaloRepeticao )
                                $configuracaoBlocoTemplateEscalaTurno->intervaloRepeticao = $bloco->blocoConfig->intervaloRepeticao;
                            else
                                return array('error'=>'Insira pelo menos um intervalo de repetição ou uma hora de execução.');
                        }
                        $this->manager->persist($configuracaoBlocoTemplateEscalaTurno);
                    }
                $this->manager->persist($configuracaoTemplateEscalaTurno);
            }
            $this->manager->flush();
        } catch (ORMException $e){
            throw new \Exception( $e->getMessage() );
        }
    }

}